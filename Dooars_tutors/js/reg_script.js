document.addEventListener('DOMContentLoaded', function () {
    const checkboxItems = document.querySelectorAll('.checkbox-item');
    checkboxItems.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                item.classList.add('checked');
            } else {
                item.classList.remove('checked');
            }
        });
    });

    initializeMap();
});

const cityBounds = {
    "Alipurduar": [
        [26.460365405979317, 89.4913932700851],     // Southwest corner
        [26.54540511209305, 89.56965539944116]      // Northeast corner
    ],
    "Coochbehar": [
        [26.30034434226509, 89.42924464849648],     // Southwest corner
        [26.36210572809391, 89.49105138590505]      // Northeast corner
    ],
    "Falakata": [
        [26.495291279660023, 89.17111036297257], // Southwest corner
        [26.549876057544523, 89.23153731687478]  // Northeast corner
    ]
};

const cityPolygons = {
    "Alipurduar": [
        {lat: 26.54540511209305, lng: 89.51237219905818},
        {lat: 26.47238471337998, lng: 89.4913932700851},
        {lat: 26.460365405979317, lng: 89.5518121667535},
        {lat: 26.532466606910887, lng: 89.56965539944116},
        {lat: 26.54540511209305, lng: 89.51237219905818}
    ],
    "Coochbehar": [
        {lat: 26.36210572809391, lng: 89.43762958867154},
        {lat: 26.307683542669793, lng: 89.42924464849648},
        {lat: 26.30034434226509, lng: 89.48075796238658},
        {lat: 26.35369605258292, lng: 89.49105138590505},
        {lat: 26.36210572809391, lng: 89.43762958867154}
    ],
    "Falakata": [
        {lat: 26.549876057544523, lng: 89.18089891349632},
        {lat: 26.50546199062704, lng: 89.17111036297257},
        {lat: 26.495291279660023, lng: 89.2216871604362},
        {lat: 26.537200466560577, lng: 89.23153731687478},
        {lat: 26.549876057544523, lng: 89.18089891349632}
    ]
};

const cityLandmarks = {
    
};

const landmarkColors = {
    // Add your landmark colors here
    "school": "#4285f4",
    "hospital": "#ea4335",
    "restaurant": "#fbbc04",
    "park": "#34a853"
};

let map;
let marker;
let geocoder;

function initializeMap() {
    // Default location (Siliguri area)
    const defaultLocation = { lat: 26.7271, lng: 88.3953 };
    
    // Initialize the map
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: defaultLocation,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    // Initialize geocoder
    geocoder = new google.maps.Geocoder();
    
    // Add click listener to map
    map.addListener('click', function(e) {
        placeMarker(e.latLng);
        getAddressFromLatLng(e.latLng);
    });
    
    // Try to get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map.setCenter(userLocation);
            placeMarker(userLocation);
            getAddressFromLatLng(userLocation);
        }, function() {
            // Handle location error - use default location
            console.log('Geolocation failed, using default location');
        });
    }
}

function placeMarker(location) {
    // Remove existing marker
    if (marker) {
        marker.setMap(null);
    }
    
    // Create new marker
    marker = new google.maps.Marker({
        position: location,
        map: map,
        draggable: true,
        title: 'Your Location'
    });
    
    // Update hidden inputs
    document.getElementById('latitude').value = location.lat();
    document.getElementById('longitude').value = location.lng();
    
    // Add drag listener to marker
    marker.addListener('dragend', function() {
        const newPosition = marker.getPosition();
        document.getElementById('latitude').value = newPosition.lat();
        document.getElementById('longitude').value = newPosition.lng();
        getAddressFromLatLng(newPosition);
    });
}

function getAddressFromLatLng(latLng) {
    geocoder.geocode({ location: latLng }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                // Update address input with formatted address
                document.getElementById('addressInput').value = results[0].formatted_address;
                
                // Try to extract city from address components
                const addressComponents = results[0].address_components;
                let city = '';
                
                for (let component of addressComponents) {
                    if (component.types.includes('locality') || 
                        component.types.includes('administrative_area_level_2')) {
                        city = component.long_name;
                        break;
                    }
                }
                
                // Auto-select city if it matches one of the options
                const citySelect = document.getElementById('citySelect');
                for (let option of citySelect.options) {
                    if (option.value.toLowerCase() === city.toLowerCase()) {
                        citySelect.value = option.value;
                        break;
                    }
                }
            }
        } else {
            console.log('Geocoder failed: ' + status);
        }
    });
}

// Optional: Add search functionality
function searchLocation() {
    const address = document.getElementById('addressInput').value;
    if (!address) {
        alert('Please enter an address to search');
        return;
    }
    
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            const location = results[0].geometry.location;
            map.setCenter(location);
            map.setZoom(15);
            placeMarker(location);
            document.getElementById('addressInput').value = results[0].formatted_address;
        } else {
            alert('Address not found: ' + status);
        }
    });
}

function getCityBoundsForGeocoding(cityName) {
    const cityKey = cityName.split(",")[0].trim();
    const bounds = cityBounds[cityKey];
    if (!bounds) return null;
    
    const [sw, ne] = bounds;
    return new google.maps.LatLngBounds(
        new google.maps.LatLng(sw[0], sw[1]),
        new google.maps.LatLng(ne[0], ne[1])
    );
}

function isInCityBounds(lat, lng, cityName) {
    const cityKey = cityName.split(",")[0].trim();
    const bounds = cityBounds[cityKey];
    if (!bounds) {
        console.warn(`No bounds defined for city: ${cityKey}`);
        return true;
    }
    const [sw, ne] = bounds;
    const inBounds = lat >= sw[0] && lat <= ne[0] && lng >= sw[1] && lng <= ne[1];
    console.log(`Checking bounds for ${cityKey}:`, { lat, lng, bounds, inBounds });
    return inBounds;
}

function reverseGeocode(lat, lng) {
    const geocoder = new google.maps.Geocoder();
    const latlng = {lat: lat, lng: lng};

    geocoder.geocode({location: latlng}, (results, status) => {
        if (status === 'OK' && results[0]) {
            document.getElementById("addressInput").value = results[0].formatted_address;
        } else {
            console.error('Reverse geocoding failed:', status);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const citySelect = document.getElementById('citySelect');
    citySelect.addEventListener('change', function () {
        const selectedCity = this.value;
        if (selectedCity) {
            const cityKey = selectedCity.split(",")[0].trim();
            const bounds = cityBounds[cityKey];

            if (bounds) {
                const [sw, ne] = bounds;
                const centerLat = (sw[0] + ne[0]) / 2;
                const centerLng = (sw[1] + ne[1]) / 2;
                
                map.setCenter({lat: centerLat, lng: centerLng});
                map.setZoom(14);

                // Remove existing polygon
                if (cityBoundaryPolygon) {
                    cityBoundaryPolygon.setMap(null);
                }

                // Add city boundary polygon
                const polygonCoords = cityPolygons[cityKey];
                if (polygonCoords) {
                    cityBoundaryPolygon = new google.maps.Polygon({
                        paths: polygonCoords,
                        strokeColor: "#7F8CAA",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#7F8CAA",
                        fillOpacity: 0.1
                    });
                    cityBoundaryPolygon.setMap(map);
                }

                addLandmarksToMap(selectedCity);
                showAlert(`${cityKey} loaded with ${cityLandmarks[cityKey]?.length || 0} landmarks. Click on any landmark or map area to select location.`, "success");
            }
        } else {
            // Remove landmarks
            landmarkMarkers.forEach(marker => marker.setMap(null));
            landmarkMarkers = [];

            // Remove city boundary
            if (cityBoundaryPolygon) {
                cityBoundaryPolygon.setMap(null);
                cityBoundaryPolygon = null;
            }
        }
    });
});

function showAlert(message, type) {
    const existingAlert = document.querySelector('.alert');
    if (existingAlert) existingAlert.remove();

    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;

    const mapSection = document.querySelector('.map-section');
    mapSection.insertBefore(alert, mapSection.firstChild);

    // setTimeout(() => alert.remove(), 5000);
}

function addMapLegend() {
    // Add your map legend implementation here if needed
    // This function was referenced in the original code but not implemented
}

// Handle "Others" option for classes
const othersCheckbox = document.getElementById('class-others');
const otherClassGroup = document.getElementById('other-class-group');

if (othersCheckbox && otherClassGroup) {
    othersCheckbox.addEventListener('change', function () {
        if (this.checked) {
            otherClassGroup.style.display = 'block';
            otherClassGroup.style.animation = 'fadeInUp 0.5s ease';
        } else {
            otherClassGroup.style.display = 'none';
            document.getElementById('other-class').value = '';
        }
    });
}

const othersSubject = document.getElementById('others');
if (othersSubject) {
    othersSubject.addEventListener('change', function () {
        const otherSpecification = document.getElementById('other-specification');
        const otherSubjectInput = document.getElementById('other-subject');

        if (this.checked) {
            otherSpecification.style.display = 'block';
            otherSubjectInput.required = true;
        } else {
            otherSpecification.style.display = 'none';
            otherSubjectInput.required = false;
            otherSubjectInput.value = '';
        }
    });
}


