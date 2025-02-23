// importing the necessary modules
const express = require('express');
const path = require('path');
const fs = require('fs');

// creating an express app
const app = express();

// importing the security
for (let file of fs.readdirSync(path.join(__dirname, 'security'))) {
    require(path.join(__dirname, 'security', file))(app);
}

// importing the middlewares
for (let file of fs.readdirSync(path.join(__dirname, 'middleware'))) {
    require(path.join(__dirname, 'middleware', file))(app);
}

// importing the routes
require(path.join(__dirname, 'routes', 'Index.js'))(app);
require(path.join(__dirname, 'routes', 'GrowtopiaWebview.js'))(app);

// 404 route
app.use((req, res) => {
    console.log(`[${new Date().toLocaleString()}] Missing file: ${req.url} [${req.method}] - ${res.statusCode}`);
    return res.sendStatus(404);
});

// exporting express app
module.exports = app;