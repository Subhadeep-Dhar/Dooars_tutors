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
                [26.54540511209305, 89.51237219905818],
                [26.47238471337998, 89.4913932700851],
                [26.460365405979317, 89.5518121667535],
                [26.532466606910887, 89.56965539944116],
                [26.54540511209305, 89.51237219905818]
            ],
            "Coochbehar": [
                [26.36210572809391, 89.43762958867154],
                [26.307683542669793, 89.42924464849648],
                [26.30034434226509, 89.48075796238658],
                [26.35369605258292, 89.49105138590505],
                [26.36210572809391, 89.43762958867154]
            ],
            "Falakata": [
                [26.549876057544523, 89.18089891349632],
                [26.50546199062704, 89.17111036297257],
                [26.495291279660023, 89.2216871604362],
                [26.537200466560577, 89.23153731687478],
                [26.549876057544523, 89.18089891349632]
            ]
        };

        const cityLandmarks = {
            "Alipurduar": [
                { name: "New Alipurduar Railway Station", lat: 26.48642, lng: 89.54156, type: "transport", icon: "🚉" },
                { name: "Alipurduar Court Station", lat: 26.501588, lng: 89.527886, type: "transport", icon: "🚉" },
                { name: "Alipurduar Station", lat: 26.478928, lng: 89.521043, type: "transport", icon: "🚉" },
                { name: "Alipurduar Junction Railway Station", lat: 26.523268, lng: 89.533703, type: "transport", icon: "🚉" },
                { name: "Buxa Tiger Reserve Office", lat: 26.4878, lng: 89.5245, type: "office", icon: "🏢" },
                { name: "Alipurduar Bus Stand", lat: 26.4932, lng: 89.5312, type: "transport", icon: "🚌" },
                { name: "Alipurduar Town Hall", lat: 26.4908, lng: 89.5285, type: "government", icon: "🏛️" },
                { name: "Alipurduar High School", lat: 26.4885, lng: 89.5265, type: "education", icon: "🏫" },
                { name: "Alipurduar Police Station", lat: 26.4915, lng: 89.5295, type: "government", icon: "👮" },
                { name: "Alipurduar Market", lat: 26.4928, lng: 89.5302, type: "market", icon: "🛒" },
                { name: "Ward 1 Office", lat: 26.4888, lng: 89.5255, type: "ward", icon: "🏛️" },
                { name: "Ward 2 Office", lat: 26.4905, lng: 89.5275, type: "ward", icon: "🏛️" },
                { name: "Ward 3 Office", lat: 26.4922, lng: 89.5295, type: "ward", icon: "🏛️" },
                { name: "Ward 4 Office", lat: 26.4938, lng: 89.5315, type: "ward", icon: "🏛️" },
                { name: "Parade Ground", lat: 26.500870, lng: 89.532598, type: "ground", icon: "🏏" },
                { name: "Indoor Stadium", lat: 26.503662, lng: 89.530517, type: "stadium", icon: "🏟" }
            ],
            "Coochbehar": [
                { name: "Coochbehar Palace", lat: 26.3244, lng: 89.4492, type: "heritage", icon: "🏰" },
                { name: "Coochbehar Railway Station", lat: 26.3389, lng: 89.4525, type: "transport", icon: "🚉" },
                { name: "Coochbehar Medical College", lat: 26.3356, lng: 89.4478, type: "education", icon: "🏥" },
                { name: "Coochbehar Government College", lat: 26.3267, lng: 89.4503, type: "education", icon: "🎓" },
                { name: "Coochbehar District Court", lat: 26.3289, lng: 89.4515, type: "government", icon: "🏛️" },
                { name: "Coochbehar Bus Terminus", lat: 26.3378, lng: 89.4512, type: "transport", icon: "🚌" },
                { name: "Coochbehar Collectorate", lat: 26.3301, lng: 89.4528, type: "government", icon: "🏢" },
                { name: "Rajbari Market", lat: 26.3256, lng: 89.4487, type: "market", icon: "🛒" },
                { name: "Coochbehar Stadium", lat: 26.3312, lng: 89.4456, type: "sports", icon: "⚽" },
                { name: "Coochbehar High School", lat: 26.3278, lng: 89.4495, type: "education", icon: "🏫" },
                { name: "Coochbehar Police Station", lat: 26.3295, lng: 89.4521, type: "government", icon: "👮" },
                { name: "Madan Mohan Temple", lat: 26.3234, lng: 89.4465, type: "religious", icon: "🛕" },
                { name: "Ward 1 - Rajbari Area", lat: 26.3245, lng: 89.4475, type: "ward", icon: "🏛️" },
                { name: "Ward 5 - College Para", lat: 26.3275, lng: 89.4505, type: "ward", icon: "🏛️" },
                { name: "Ward 10 - Station Area", lat: 26.3385, lng: 89.4520, type: "ward", icon: "🏛️" }
            ],
            "Falakata": [
                { name: "Falakata Railway Station", lat: 26.5289, lng: 89.2089, type: "transport", icon: "🚉" },
                { name: "Falakata Block Office", lat: 26.5298, lng: 89.2105, type: "government", icon: "🏢" },
                { name: "Falakata High School", lat: 26.5275, lng: 89.2085, type: "education", icon: "🏫" },
                { name: "Falakata Hospital", lat: 26.5305, lng: 89.2112, type: "hospital", icon: "🏥" },
                { name: "Falakata Police Station", lat: 26.5285, lng: 89.2095, type: "government", icon: "👮" },
                { name: "Falakata Bus Stand", lat: 26.5312, lng: 89.2125, type: "transport", icon: "🚌" },
                { name: "Falakata Market", lat: 26.5292, lng: 89.2102, type: "market", icon: "🛒" },
                { name: "Falakata College", lat: 26.5265, lng: 89.2075, type: "education", icon: "🎓" },
                { name: "Falakata Post Office", lat: 26.5288, lng: 89.2098, type: "government", icon: "📮" },
                { name: "Falakata Stadium", lat: 26.5315, lng: 89.2135, type: "sports", icon: "⚽" },
                { name: "Ward 1 Office", lat: 26.5268, lng: 89.2072, type: "ward", icon: "🏛️" },
                { name: "Ward 2 Office", lat: 26.5282, lng: 89.2088, type: "ward", icon: "🏛️" },
                { name: "Ward 3 Office", lat: 26.5295, lng: 89.2108, type: "ward", icon: "🏛️" },
                { name: "Ward 4 Office", lat: 26.5308, lng: 89.2128, type: "ward", icon: "🏛️" }
            ]
        };

        const landmarkColors = {
            "transport": "#c2c1a5",
            "education": "#c2c1a5",
            "government": "#c2c1a5",
            "hospital": "#c2c1a5",
            "market": "#c2c1a5",
            "heritage": "#c2c1a5",
            "religious": "#c2c1a5",
            "sports": "#c2c1a5",
            "office": "#c2c1a5",
            "ward": "#c2c1a5",
            "ground": "#c2c1a5",
            "stadium": "#c2c1a5",
        };

        let map, marker = null, landmarkMarkers = [], cityBoundaryRect = null;

        function initializeMap() {
            map = L.map('map').setView([26.5, 89.5], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © OpenStreetMap contributors',
                maxZoom: 18
            }).addTo(map);

            map.on('click', onMapClick);
            setTimeout(() => map.invalidateSize(), 100);
            addMapLegend();
        }

        function addMapLegend() {
            const legend = L.control({ position: 'bottomleft' });
            legend.onAdd = function () {
                const div = L.DomUtil.create('div', 'map-legend');
                div.style.cssText = `
                background: white; padding: 10px; border-radius: 8px; 
                box-shadow: 0 2px 10px rgba(0,0,0,0.1); font-size: 12px; line-height: 18px;
            `;
                div.innerHTML = `
                <div style="font-weight: bold; margin-bottom: 5px; color: #333446;">🗺️ Map Legend</div>
                <div>🚉 Transport Hub</div>
                <div>🎓 Colleges</div>
                <div>🏫 Schools</div>
                <div>🏥 Hospitals</div>
                <div>🏛️ Government Offices</div>
                <div>🛒 Markets</div>
                <div>📍 Click to select location</div>
            `;
                return div;
            };
            legend.addTo(map);
        }

        function addLandmarksToMap(cityName) {
            landmarkMarkers.forEach(marker => map.removeLayer(marker));
            landmarkMarkers = [];

            const cityKey = cityName.split(",")[0].trim();
            const landmarks = cityLandmarks[cityKey];
            if (!landmarks) return;

            landmarks.forEach(landmark => {
                const color = landmarkColors[landmark.type] || "#333446";
                const customIcon = L.divIcon({
                    className: 'landmark-icon',
                    html: `
                    <div style="
                        background: ${color}; border-radius: 50%; width: 30px; height: 30px;
                        display: flex; align-items: center; justify-content: center; font-size: 14px;
                        border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    ">${landmark.icon}</div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                const landmarkMarker = L.marker([landmark.lat, landmark.lng], {
                    icon: customIcon
                }).addTo(map);

                landmarkMarker.bindPopup(`
                <div style="font-family: Inter, sans-serif;">
                    <strong style="color: #333446;">${landmark.name}</strong><br>
                    <small style="color: #7F8CAA;">Type: ${landmark.type}</small><br>
                    <button onclick="selectLandmarkLocation(${landmark.lat}, ${landmark.lng}, '${landmark.name}')" 
                        style="background: #333446; color: white; border: none; padding: 5px 10px; 
                        border-radius: 4px; cursor: pointer; margin-top: 5px; font-size: 12px;">
                        📍 Select This Location
                    </button>
                </div>`);
                landmarkMarkers.push(landmarkMarker);
            });
        }

        window.selectLandmarkLocation = function (lat, lng, name) {
            if (!marker) {
                marker = L.marker([lat, lng]).addTo(map);
            } else {
                marker.setLatLng([lat, lng]);
            }

            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
            document.getElementById("addressInput").value = name;
            map.closePopup();
            showAlert(`Location selected: ${name}`, "success");
        };

        function onMapClick(e) {
            const lat = e.latlng.lat;
            const lon = e.latlng.lng;
            const citySelect = document.getElementById('citySelect');
            const selectedCity = citySelect.value;

            if (selectedCity && !isInCityBounds(lat, lon, selectedCity)) {
                showAlert("Please click within the selected city boundaries.", "error");
                return;
            }

            if (!marker) {
                marker = L.marker([lat, lon]).addTo(map);
            } else {
                marker.setLatLng([lat, lon]);
            }

            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lon;
            reverseGeocode(lat, lon);
            showAlert("Location selected successfully!", "success");
        }

        function searchLocation() {
            const city = document.getElementById('citySelect').value;
            const address = document.getElementById('addressInput').value;
            if (!city || !address) {
                showAlert("Please select city and enter address.", "error");
                return;
            }

            const searchBtn = document.querySelector('.btn-secondary');
            const originalText = searchBtn.textContent;
            searchBtn.textContent = "🔍 Searching...";
            searchBtn.disabled = true;

            const query = encodeURIComponent(`${address}, ${city}`);
            const url = `https://photon.komoot.io/api/?q=${query}&limit=5`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        let locationFound = false;
                        for (let feature of data.features) {
                            const [lon, lat] = feature.geometry.coordinates;
                            if (isInCityBounds(lat, lon, city)) {
                                map.setView([lat, lon], 16);
                                if (!marker) marker = L.marker([lat, lon]).addTo(map);
                                else marker.setLatLng([lat, lon]);
                                document.getElementById("latitude").value = lat;
                                document.getElementById("longitude").value = lon;
                                showAlert("Location found successfully!", "success");
                                locationFound = true;
                                break;
                            }
                        }

                        if (!locationFound) {
                            const [lon, lat] = data.features[0].geometry.coordinates;
                            map.setView([lat, lon], 16);
                            if (!marker) marker = L.marker([lat, lon]).addTo(map);
                            else marker.setLatLng([lat, lon]);
                            document.getElementById("latitude").value = lat;
                            document.getElementById("longitude").value = lon;
                            showAlert("Location found, but may be outside city boundaries. Please verify.", "error");
                        }
                    } else {
                        showAlert("Address not found. Please try a different search term.", "error");
                    }
                })
                .catch(err => {
                    console.error('Search error:', err);
                    showAlert("Error searching location. Please try again.", "error");
                })
                .finally(() => {
                    searchBtn.textContent = originalText;
                    searchBtn.disabled = false;
                });
        }

        function isInCityBounds(lat, lon, cityName) {
            const cityKey = cityName.split(",")[0].trim();
            const bounds = cityBounds[cityKey];
            if (!bounds) {
                console.warn(`No bounds defined for city: ${cityKey}`);
                return true;
            }
            const [sw, ne] = bounds;
            const inBounds = lat >= sw[0] && lat <= ne[0] && lon >= sw[1] && lon <= ne[1];
            console.log(`Checking bounds for ${cityKey}:`, { lat, lon, bounds, inBounds });
            return inBounds;
        }

        function reverseGeocode(lat, lon) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById("addressInput").value = data.display_name;
                    }
                })
                .catch(err => {
                    console.error('Reverse geocoding error:', err);
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
                        const centerLon = (sw[1] + ne[1]) / 2;
                        map.setView([centerLat, centerLon], 14);

                        if (cityBoundaryRect) {
                            map.removeLayer(cityBoundaryRect);
                        }

                        const polygonCoords = cityPolygons[cityKey];
                        if (polygonCoords) {
                            cityBoundaryRect = L.polygon(polygonCoords, {
                                color: "#7F8CAA",
                                weight: 2,
                                opacity: 0.8,
                                fillOpacity: 0.1
                            }).addTo(map);
                        }

                        addLandmarksToMap(selectedCity);
                        showAlert(`${cityKey} loaded with ${cityLandmarks[cityKey]?.length || 0} landmarks. Click on any landmark or map area to select location.`, "success");
                    }
                } else {
                    landmarkMarkers.forEach(marker => map.removeLayer(marker));
                    landmarkMarkers = [];

                    if (cityBoundaryRect) {
                        map.removeLayer(cityBoundaryRect);
                        cityBoundaryRect = null;
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

            setTimeout(() => alert.remove(), 5000);
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

        document.getElementById('others').addEventListener('change', function () {
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