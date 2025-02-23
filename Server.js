// Importing the necessary modules
const path = require('path');
const app = require(path.join(__dirname, 'MainApp.js'));
const https = require('https');
const fs = require('fs');
const tls = require('tls');

const sniDefaultCert = fs.readFileSync(path.join(__dirname, 'certs/_wildcard.gtps.pw.pem'));
const sniDefaultKey = fs.readFileSync(path.join(__dirname, 'certs/_wildcard.gtps.pw-key.pem'));

const sniCallback = (serverName, callback) => {
	console.log(serverName);
	let cert = null;
	let key = null;

	if (serverName != 'www.growtopia1.com' && serverName != 'www.growtopia2.com') {
        cert = sniDefaultCert;
		key = sniDefaultKey;
	} else {
        cert = fs.readFileSync(path.join(__dirname, 'certs/gt.pem'));
		key = fs.readFileSync(path.join(__dirname, 'certs/gt-key.pem'));
	}

	callback(null, new tls.createSecureContext({
		cert,
		key,
	}));
}

const serverOptions = {
	SNICallback: sniCallback,
}

// starting server with port 80
app.listen(80, () => {
    console.log('Server started at http://localhost:3000');
});

// starting secure server with port 443
https.createServer(serverOptions, app).listen(443, () => {
    console.log('Secure server started at https://localhost:443');
});