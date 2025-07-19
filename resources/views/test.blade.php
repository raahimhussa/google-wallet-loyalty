<!DOCTYPE html>
<html>
<head>
    <title>Google Wallet Loyalty Card Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div style="padding: 20px; max-width: 800px; margin: 0 auto;">
        <h1>Google Wallet Loyalty Card Test</h1>
        
        <div style="margin-bottom: 20px;">
            <h2>Create Loyalty Card</h2>
            <input type="text" id="userName" placeholder="User Name" value="Test User">
            <button onclick="createCard()">Create Card</button>
        </div>
        
        <div id="cardResult" style="margin-bottom: 20px;"></div>
        
        <div style="margin-bottom: 20px;">
            <h2>Update Points</h2>
            <input type="text" id="cardId" placeholder="Card ID">
            <input type="number" id="points" placeholder="Points" value="100">
            <button onclick="updatePoints()">Update Points</button>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h2>Send Push Notification</h2>
            <input type="text" id="pushCardId" placeholder="Card ID">
            <input type="text" id="pushTitle" placeholder="Title" value="Test Notification">
            <input type="text" id="pushMessage" placeholder="Message" value="This is a test notification">
            <input type="text" id="deviceTokens" placeholder="Device Tokens (comma separated)">
            <button onclick="sendPushNotification()">Send Push</button>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h2>Send Geo Notification</h2>
            <input type="text" id="geoCardId" placeholder="Card ID">
            <input type="text" id="geoTitle" placeholder="Title" value="Near Store">
            <input type="text" id="geoMessage" placeholder="Message" value="Visit us to earn points!">
            <input type="number" id="latitude" placeholder="Latitude" value="37.7749" step="any">
            <input type="number" id="longitude" placeholder="Longitude" value="-122.4194" step="any">
            <input type="number" id="radius" placeholder="Radius (meters)" value="100">
            <button onclick="sendGeoNotification()">Send Geo</button>
        </div>
        
        <div id="result" style="margin-top: 20px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9;"></div>
    </div>

    <script>
        function createCard() {
            const userName = document.getElementById('userName').value;
            
            axios.post('/api/loyalty-cards', {
                user_name: userName
            })
            .then(response => {
                const data = response.data;
                document.getElementById('result').innerHTML = `
                    <h3>Card Created Successfully!</h3>
                    <p><strong>Card ID:</strong> ${data.data.card.card_id}</p>
                    <p><strong>Card Number:</strong> ${data.data.card.card_number}</p>
                    <p><strong>Points:</strong> ${data.data.card.points}</p>
                    <p><strong>Save to Google Wallet:</strong> <a href="${data.data.save_link}" target="_blank">Click Here</a></p>
                `;
                
                // Auto-fill card ID for other operations
                document.getElementById('cardId').value = data.data.card.card_id;
                document.getElementById('pushCardId').value = data.data.card.card_id;
                document.getElementById('geoCardId').value = data.data.card.card_id;
            })
            .catch(error => {
                document.getElementById('result').innerHTML = `
                    <h3>Error Creating Card</h3>
                    <p style="color: red;">${error.response?.data?.message || error.message}</p>
                `;
            });
        }

        function updatePoints() {
            const cardId = document.getElementById('cardId').value;
            const points = document.getElementById('points').value;
            
            axios.patch(`/api/loyalty-cards/${cardId}/points`, {
                points: parseInt(points)
            })
            .then(response => {
                document.getElementById('result').innerHTML = `
                    <h3>Points Updated Successfully!</h3>
                    <p><strong>Card ID:</strong> ${response.data.data.card_id}</p>
                    <p><strong>New Points:</strong> ${response.data.data.points}</p>
                `;
            })
            .catch(error => {
                document.getElementById('result').innerHTML = `
                    <h3>Error Updating Points</h3>
                    <p style="color: red;">${error.response?.data?.message || error.message}</p>
                `;
            });
        }

        function sendPushNotification() {
            const cardId = document.getElementById('pushCardId').value;
            const title = document.getElementById('pushTitle').value;
            const message = document.getElementById('pushMessage').value;
            const deviceTokens = document.getElementById('deviceTokens').value.split(',').map(t => t.trim());
            
            axios.post('/api/notifications/push', {
                card_id: cardId,
                title: title,
                message: message,
                device_tokens: deviceTokens
            })
            .then(response => {
                document.getElementById('result').innerHTML = `
                    <h3>Push Notification Sent!</h3>
                    <p><strong>Success:</strong> ${response.data.data.success || 0}</p>
                    <p><strong>Failure:</strong> ${response.data.data.failure || 0}</p>
                `;
            })
            .catch(error => {
                document.getElementById('result').innerHTML = `
                    <h3>Error Sending Push Notification</h3>
                    <p style="color: red;">${error.response?.data?.message || error.message}</p>
                `;
            });
        }

        function sendGeoNotification() {
            const cardId = document.getElementById('geoCardId').value;
            const title = document.getElementById('geoTitle').value;
            const message = document.getElementById('geoMessage').value;
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            const radius = document.getElementById('radius').value;
            const deviceTokens = document.getElementById('deviceTokens').value.split(',').map(t => t.trim());
            
            axios.post('/api/notifications/geo', {
                card_id: cardId,
                title: title,
                message: message,
                latitude: parseFloat(latitude),
                longitude: parseFloat(longitude),
                radius: parseInt(radius),
                device_tokens: deviceTokens
            })
            .then(response => {
                document.getElementById('result').innerHTML = `
                    <h3>Geo Notification Sent!</h3>
                    <p><strong>Success:</strong> ${response.data.data.success || 0}</p>
                    <p><strong>Failure:</strong> ${response.data.data.failure || 0}</p>
                    <p><strong>Location:</strong> ${latitude}, ${longitude}</p>
                    <p><strong>Radius:</strong> ${radius}m</p>
                `;
            })
            .catch(error => {
                document.getElementById('result').innerHTML = `
                    <h3>Error Sending Geo Notification</h3>
                    <p style="color: red;">${error.response?.data?.message || error.message}</p>
                `;
            });
        }
    </script>
</body>
</html>