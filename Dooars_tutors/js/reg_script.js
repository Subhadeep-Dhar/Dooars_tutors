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
        { name: "New Alipurduar Railway Station", lat: 26.486342, lng: 89.541450, type: "transport", icon: "🚉" },
        { name: "Alipurduar Court Station", lat: 26.501588, lng: 89.527886, type: "transport", icon: "🚉" },
        { name: "Alipurduar Station", lat: 26.478928, lng: 89.521043, type: "transport", icon: "🚉" }, // Changed
        { name: "Alipurduar Junction Railway Station", lat: 26.523268, lng: 89.533703, type: "transport", icon: "🚉" },

        { name: "Alipurduar University", lat: 26.50138, lng: 89.53870, type: "education", icon: "🎓" },
        { name: "Jamai Dokan", lat: 26.49677, lng: 89.53594, type: "market", icon: "JD" },
        { name: "Dooars Kanya", lat: 26.50359, lng: 89.53154, type: "government", icon: "DK" },
        { name: "NBSTC", lat: 26.50382, lng: 89.52954, type: "transport", icon: "NB" },
        { name: "Alipurduar Fire Station", lat: 26.50238, lng: 89.52948, type: "fire station", icon: "🚒" },
        { name: "Alipurduar Court", lat: 26.50030, lng: 89.53027, type: "ward", icon: "🏛️" },
        { name: "Mc William High School", lat: 26.496367, lng: 89.526762, type: "education", icon: "MC" },
        { name: "New Town Durga Bari", lat: 26.495061, lng: 89.530205, type: "religious", icon: "ૐ" },
        { name: "Buxa Feeder", lat: 26.494182, lng: 89.526558, type: "hotel", icon: "BF" },
        { name: "New Town Bazaar", lat: 26.49410, lng: 89.53014, type: "market", icon: "🛒" },
        { name: "Go Green Nursery", lat: 26.49351, lng: 89.53308, type: "education", icon: "🏫" },
        { name: "Mahakal Dham", lat: 26.49220, lng: 89.52705, type: "religious", icon: "MD" },
        { name: "Lohar Pul Mandir", lat: 26.49187, lng: 89.53220, type: "religious", icon: "LP" },
        { name: "Govinda High School", lat: 26.49174, lng: 89.53338, type: "education", icon: "HS" },
        { name: "Power House pukur", lat: 26.49220, lng: 89.53424, type: "other", icon: "PH" },
        { name: "Madhyapara Housing Complex", lat: 26.495397, lng: 89.536300, type: "hotel", icon: "HC" },
        { name: "Kanchenjungha Apartment", lat: 26.49206, lng: 89.53626, type: "hotel", icon: "KA" },
        { name: "Indoor Stadium", lat: 26.503662, lng: 89.530517, type: "stadium", icon: "🏟" },
        { name: "N.F.Railway Community Hall",lat: 26.53257386175455,lng: 89.53820843372694,type: "government",icon: "🏛️"},
        { name: "Rly. Community Hall, Alipurduar Jn.",lat: 26.53267037938547,lng: 89.5373916319224,type: "government",icon: "🏛️"},
        
        { name: "Alipurduar District Hospital", lat: 26.484184, lng: 89.523363, type: "hospital", icon: "🏥" },
        { name: "Alipurduar Lions Eye Hospital", lat: 26.482253, lng: 89.527947, type: "hospital", icon: "🏥" },
        { name: "Yashoda Hospitals Medical Information Centre", lat: 26.486204, lng: 89.523746, type: "hospital", icon: "🏥" },
        { name: "Divisional Railway Hospital", lat: 26.524767, lng: 89.536621, type: "hospital", icon: "🏥" },
        { name: "AYUSH Hospital", lat: 26.505319, lng: 89.433257, type: "hospital", icon: "🏥" },
        { name: "Greenland Nursing Home Pvt Ltd", lat: 26.481429, lng: 89.524212, type: "hospital", icon: "🏥" },
        { name: "St Mary's Nursing Home", lat: 26.481961, lng: 89.527297, type: "hospital", icon: "🏥" },
        { name: "Shree Krishna Nursing Home", lat: 26.483569, lng: 89.524710, type: "hospital", icon: "🏥" },
        { name: "Pulse Sanjivani Nursing Home", lat: 26.483972, lng: 89.526435, type: "hospital", icon: "🏥" },
        { name: "JD Healthcare Nursing Home & Diagnostic Centre", lat: 26.485248, lng: 89.525230, type: "hospital", icon: "🏥" },
        { name: "ALIPURDUAR MISTI AAYA CENTRE & HOME CARE NURSING", lat: 26.520909, lng: 89.555695, type: "hospital", icon: "🏥" },
        { name: "New Sri Krishna Nursing Home", lat: 26.484533, lng: 89.525876, type: "hospital", icon: "🏥" },
        { name: "Alipurduar Aya Service Centre", lat: 26.525635, lng: 89.556233, type: "hospital", icon: "🏥" },
        { name: "Alipurduar District Hospital Nursing Training School", lat: 26.483112, lng: 89.522342, type: "education", icon: "🏫" },
                
                
        {name: "IndianOil",lat: 26.480376009807067,lng: 89.5344585691566,type: "fuel",icon: "⛽"},
        {name: "Bharat Petrol Pump",lat: 26.48202529689521,lng: 89.51123213043049,type: "fuel",icon: "⛽"},
        {name: "IndianOil",lat: 26.48077899598905,lng: 89.53784900002093,type: "fuel",icon: "⛽"},
        {name: "IndianOil",lat: 26.480788599197922,lng: 89.53785168222998,type: "fuel",icon: "⛽"},
        {name: "Jio-bp",lat: 26.488528430707955,lng: 89.5015995435023,type: "fuel",icon: "⛽"},
        {name: "Bharat Petrol Pump",lat: 26.47279210193813,lng: 89.55013204244035,type: "fuel",icon: "⛽"},
        {name: "IndianOil",lat: 26.479697699175134,lng: 89.50846234111498,type: "fuel",icon: "⛽"},
        {name: "Bharat Petrol Pump - N.N ROY SERVICE STATION",lat: 26.467141597282875,lng: 89.57747500001045,type: "fuel",icon: "⛽"},
        {name: "IndianOil",lat: 26.535167400501177,lng: 89.53630300001045,type: "fuel",icon: "⛽"},
        {name: "Public Bus Station",lat: 26.478919315036542,lng: 89.52179060333211,type: "fuel",icon: "⛽"},
        
        {name: "Alipurduar Bus Stand",lat: 26.479864652335255,lng: 89.53551890411389,type: "transport",icon: "🚌"},
        {name: "Alipurduar-Cochbehar Bus Stand",lat: 26.519487823039068,lng: 89.20291890001343,type: "transport",icon: "🚌"},
        
        {name: "Alipurduar College Ground", lat: 26.500812, lng: 89.535945, type: "ground", icon: "🌲" },
        {name: "Surya Nagar Ground",lat: 26.497193612374762,lng: 89.52292890008374,type: "ground",icon: "🌲"},
        {name: "Town Club Ground",lat: 26.476205696409306,lng: 89.5235520680222,type: "ground",icon: "🌲"},
        {name: "DRM GROUND",lat: 26.534929698684024,lng: 89.5378923000209,type: "ground",icon: "🌲"},
        {name: "Arabinda Nagar Play Ground",lat: 26.502588501387994,lng: 89.5261914466481,type: "ground",icon: "🌲"},
        {name: "BMC Ground",lat: 26.477936995692403,lng: 89.52618471781186,type: "ground",icon: "🌲"},
        {name: "Udayan Bitan Ground",lat: 26.498833998724265,lng: 89.52480600002093,type: "ground",icon: "🌲"},
        {name: "SHANTINAGAR, UPAL MUKHAR GROUND",lat: 26.483449797760546,lng: 89.52845100002092,type: "ground",icon: "🌲"},
        {name: "Asutosh Club Field",lat: 26.47609039909986,lng: 89.51771941781183,type: "ground",icon: "🌲"},
        {name: "Ananda nagar play ground",lat: 26.481912200000007,lng: 89.53402423560279,type: "ground",icon: "🌲"},
        {name: "Netaji Vidyapith Playground",lat: 26.51331950036827,lng: 89.53288924664811,type: "ground",icon: "🌲"},
        {name: "NABIN CLUB FOOTBALL GROUND",lat: 26.509018599575,lng: 89.53140571781185,type: "ground",icon: "🌲"},
        
        {name: "Alipurduar Park",lat: 26.47754536871918,lng: 89.52504886191598,type: "park",icon: "🛝"},
        {name: "Rabindra Shishu Udyan",lat: 26.497296452901598,lng: 89.52758180508614,type: "park",icon: "🛝"},
        {name: "Netaji PARK",lat: 26.496715049500697,lng: 89.5272949495227,type: "park",icon: "🛝"},
        {name: "Gol Park",lat: 26.532053354559768,lng: 89.53765747779273,type: "park",icon: "🛝"},
        {name: "Choto Gol Park",lat: 26.533456305659172,lng: 89.53830860752493,type: "ground",icon: "🌲"},
        {name: "Parade Ground", lat: 26.500870, lng: 89.532598, type: "ground", icon: "PG" },
        {name: "Maa Paradise World",lat: 26.51663989968116,lng: 89.5701440246104,type: "park",icon: "🛝"},

        {name: "Alipurduar Super Market", lat: 26.48060585729641, lng: 89.52714132731707,type: "market",icon: "🛒"},
        {name: "New Town Bazaar", lat: 26.493049399999993, lng: 89.52838907838527,type: "market",icon: "🛒"},
        {name: "New-Alipur station Fancy Market", lat: 26.486144999654876, lng: 89.53993709878748,type: "market",icon: "🛒"},
        {name: "Radha Madhab Mondir Market", lat: 26.491266093585963, lng: 89.5407733178014,type: "market",icon: "🛒"},
        {name: "Lichutala Bazaar", lat: 26.515224700000008, lng: 89.53255034111498,type: "market",icon: "🛒"},
        {name: "Alipurduar Jn. Railway Market", lat: 26.52874299990015, lng: 89.5351764822195,type: "market",icon: "🛒"},
        {name: "Birpara Market", lat: 26.482853898861535, lng: 89.50850668221952,type: "market",icon: "🛒"},
        {name: "Alipurduar Daily Fish Market", lat: 26.480925210394712, lng: 89.52097864663763,type: "market",icon: "🛒"},
        {name: "Boro Bazar", lat: 26.480740348702565, lng: 89.52071847235861,type: "market",icon: "🛒"},
        {name: "Bou Bazaar", lat: 26.498831599681107, lng: 89.52779460008368,type: "market",icon: "🛒"},
        {name: "Cosmo Bazaar Alipurduar", lat: 26.479932772541304, lng: 89.52643743721862,type: "maarket",icon: "🛒"},
        {name: "STYLE BAAZAR ALIPURDUAR", lat: 26.48063529959736, lng: 89.52432631036318,type: "maarket",icon: "🛒"},
        {name: "TRENDS", lat: 26.484796899999996, lng: 89.52654410001045,type: "maarket",icon: "🛒"},
        {name: "PRIMART-Alipurduar", lat: 26.489165000000014, lng: 89.52663170001046,type: "maarket",icon: "🛒"},
        {name: "Reliance Smart Point Alipurduar", lat: 26.495407600248466, lng: 89.52784610001045,type: "maarket",icon: "🛒"},
        {name: "Dipak Shopping Mall", lat: 26.685724500000024, lng: 89.42206210001045,type: "maarket",icon: "🛒"},
        {name: "FASHION FUSION ALIPURDUAR", lat: 26.480462296764685, lng: 89.52298800001046,type: "maarket",icon: "🛒"},
        {name: "Bharat Stores", lat: 26.480579998790358, lng: 89.52515374111495,type: "maarket",icon: "🛒"},
        {name: "Senco Gold & Diamonds- AlipurDuar", lat: 26.49891360042367, lng: 89.52748604111498,type: "maarket",icon: "🛒"},
        {name: "ORIENT JEWELLERS, ALIPURDUAR", lat: 26.494531800772815, lng: 89.52712732762373,type: "maarket",icon: "🛒"},
        
        { name: "Banga Bhumi", lat: 26.502099, lng: 89.528435, type: "hotel", icon: "HB" },
        {name: "Hotel Ratneshwar", lat: 26.48406824013886, lng: 89.5063995890738,type: "hotel",icon: "🏢"},
        {name: "Hotel Dooars Mountain", lat: 26.488258098694256, lng: 89.52669167349582,type: "hotel",icon: "🏢"},
        {name: "HOTEL PALACIO REGENCY", lat: 26.491954795449676, lng: 89.49381708251529,type: "hotel",icon: "🏢"},
        {name: "HOTEL ELITE", lat: 26.48056859516062, lng: 89.52828143562374,type: "hotel",icon: "🏢"},
        {name: "Hotel Dooars Hillton", lat: 26.480999814846086, lng: 89.534053622498,type: "hotel",icon: "🏢"},
        {name: "Hotel Maa international", lat: 26.480844400000002, lng: 89.5352623646206,type: "hotel",icon: "🏢"},
        {name: "OYO Hotel Chitra", lat: 26.48308699773781, lng: 89.5266125711847,type: "hotel",icon: "🏢"},
        {name: "Hotel Sinchula", lat: 26.482976562994327, lng: 89.52726166577771,type: "hotel",icon: "🏢"},
        {name: "OYO 62896 Hotel Ridhi Sidhi", lat: 26.47966800330245, lng: 89.52219836013936,type: "hotel",icon: "🏢"}


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
    "post office": "#c2c1a5",
    "police station": "#c2c1a5",
    "fire station": "#c2c1a5",
    "hotel": "#c2c1a5",
    "religious": "#c2c1a5",
    "other": "#c2c1a5",
    "fuel": "#c2c1a5",
    "park": "#c2c1a5",
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

// function addMapLegend() {
//     const legend = L.control({ position: 'bottomleft' });
//     legend.onAdd = function () {
//         const div = L.DomUtil.create('div', 'map-legend');
//         div.style.cssText = `
//                 background: white; padding: 10px; border-radius: 8px; 
//                 box-shadow: 0 2px 10px rgba(0,0,0,0.1); font-size: 12px; line-height: 18px;
//             `;
//         div.innerHTML = `
//                 <div style="font-weight: bold; margin-bottom: 5px; color: #333446;">🗺️ Map Legend</div>
//                 <div>🚉 Transport Hub</div>
//                 <div>🎓 Colleges</div>
//                 <div>🏫 Schools</div>
//                 <div>🏥 Hospitals</div>
//                 <div>🏛️ Government Offices</div>
//                 <div>🛒 Markets</div>
//                 <div>📍 Click to select location</div>
//             `;
//         return div;
//     };
//     legend.addTo(map);
// }

function addLandmarksToMap(cityName) {
    landmarkMarkers.forEach(marker => map.removeLayer(marker));
    landmarkMarkers = [];

    const cityKey = cityName.split(",")[0].trim();
    const landmarks = cityLandmarks[cityKey];
    if (!landmarks) return;

    landmarks.forEach(landmark => {
        const color = landmarkColors[landmark.type] || "#c2c1a5";
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