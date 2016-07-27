var Service, Characteristic, LastUpdate;

module.exports = function(homebridge) {
    Service = homebridge.hap.Service;
    Characteristic = homebridge.hap.Characteristic;
    homebridge.registerPlatform("homebridge-homesecurity-server", "HomeSecurityServer", Server);
}

function Server(log, config) {
    this.config = config;
    this.log = log;
    var exec = require('child_process').exec;

    exec('python server.py', (error, stdout, stderr) => {
        if (error) {
            console.error(`exec error: ${error}`);
            return;
        }
        console.log("HomeSecurityServer is listening on port 5000");
        console.log(`stdout: ${stdout}`);
        console.log(`stderr: ${stderr}`);
    });
}
Server.prototype.accessories = function(callback) {
    var self = this;
    this.accessories = [];
    callback(this.accessories);
}
