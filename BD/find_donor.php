<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: signin.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Find Nearby Donors | RedPulse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f44336;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .form-box {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 15px;
            max-width: 500px;
            margin: auto;
        }
        #map {
            height: 600px;
            width: 100%;
            margin-top: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<h2>Find Nearby Donors</h2>

<div class="form-box">
    <label for="blood">Select Blood Group:</label>
    <select id="blood">
        <option value="">--Select--</option>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
    </select>
    <button onclick="findNearbyDonors()">Search</button>
</div>

<div id="map"></div>

<script>
let map, userLat, userLng;

function initMap() {
    // Initialize map with placeholder location
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 23.8103, lng: 90.4125 },
        zoom: 10,
    });

    // Get current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;

            map.setCenter({ lat: userLat, lng: userLng });
            new google.maps.Marker({
                position: { lat: userLat, lng: userLng },
                map: map,
                title: "You are here",
                icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
            });
        });
    } else {
        alert("Geolocation not supported by your browser.");
    }
}

function findNearbyDonors() {
    const bloodtype = document.getElementById('blood').value;
    if (!bloodtype || userLat === undefined) {
        alert('Please select a blood group and allow location access.');
        return;
    }

    fetch(`fetch_nearby_donors.php?bloodtype=${bloodtype}&lat=${userLat}&lng=${userLng}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(donor => {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(donor.latitude), lng: parseFloat(donor.longitude) },
                    map: map,
                    title: donor.name
                });

                const info = new google.maps.InfoWindow({
                    content: `<strong>${donor.name}</strong><br>
                              Blood Group: ${donor.bloodtype}<br>
                              Contact: ${donor.contact}<br>
                              Email: ${donor.email}<br>
                              Last Donation: ${donor.lastdonationdate}`
                });

                marker.addListener('click', () => {
                    info.open(map, marker);
                });
            });
        })
        .catch(err => console.log(err));
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-C9LJDN3BLTzepnHCglYmDxjc0lo6rRA&callback=initMap" async defer></script>

</body>
</html>
