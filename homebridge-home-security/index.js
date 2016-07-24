var Service, Characteristic;
var request = require('sync-request');

module.exports = function(homebridge) {
    Service = homebridge.hap.Service;
    Characteristic = homebridge.hap.Characteristic;
    homebridge.registerAccessory("homebridge-home-security", "HomeSecurity", HomeSecurity);
}


function HomeSecurity(log, config) {
    this.log = log;
    this.targetState = 3;
	this.current = 0;

    // url info
    this.url = config["url"] || "";
    this.dpin = config["dpin"] || "";
    this.http_method = config["http_method"] || "GET";
    this.name = config["name"];
    this.type = config["type"];
    this.manufacturer = config["manufacturer"] || "JcBriones Home Security";
    this.model = config["model"] || "Model not available";
    this.serial = config["serial"] || "Non-defined serial";
}

HomeSecurity.prototype = {

    httpRequest: function(url, body, method, username, password, sendimmediately, callback) {
        cons
        request({
                url: url,
                body: body,
                method: method,
                rejectUnauthorized: false
            },
            function(error, response, body) {
                callback(error, response, body)
            })
    },

    getCurrentState: function(callback) {
        var res = request(this.http_method, this.url + '/get/' + this.dpin, {});
        if (res.statusCode >= 400) {
            this.log('HTTP getCurrentState function failed');
            callback(error);
        } else {
            var info = JSON.parse(res.body);

            if (this.type == "contact") {
                this.securityService.setCharacteristic(
                    Characteristic.ContactSensorState,
                    info.val
                );
				this.current = info.val;
				this.log('HTTP getTargetState function succeeded! Data: ' + info.val);
            } else if (this.type == "motion") {
                this.securityService.setCharacteristic(
                    Characteristic.MotionDetected,
                    info.val
                );
				this.current = info.val;
				this.log('HTTP getTargetState function succeeded! Data: ' + info.val);
            } else if (this.type == "security") {
                this.securityService.setCharacteristic(
                    Characteristic.SecuritySystemCurrentState,
                    info.val
                );
				this.current = info.val;
				this.log('HTTP getTargetState function succeeded! Data: ' + info.val);
            }
            callback(null, info.val);
        }
    },

    getTargetState: function(callback) {
        var res = request(this.http_method, this.url + '/get/' + this.dpin, {});
        if (res.statusCode >= 400) {
            this.log('HTTP getTargetState function failed');
            callback(error);
        } else {
            var info = JSON.parse(res.body);

            if (this.type == "security" && info.val != 4 && this.targetState != info.val) {
                this.securityService.setCharacteristic(
                    Characteristic.SecuritySystemCurrentState,
                    info.val
                );
                this.log('HTTP getTargetState function succeeded! Data: ' + info.val);
                this.targetState = info.val;
			}
			callback(null, this.targetState);
        }
    },
	
    setTargetState: function(value, callback) {
        var res = request(this.http_method, this.url + '/' + this.dpin + '/' + value, {});
        if (res.statusCode >= 400) {
            this.log('HTTP setTargetState function failed');
            callback(error);
        } else {
            var info = JSON.parse(res.body);

            if (this.type == "security") {
                this.securityService.setCharacteristic(
                    Characteristic.SecuritySystemCurrentState,
                    value
                );
				this.current = value;
                this.log('HTTP setTargetState function succeeded! Data: ' + value);
            }
            callback();
        }
    },

    identify: function(callback) {
        this.log("Identify requested!");
        callback(); // success
    },

    getServices: function() {
        this.informationService = new Service.AccessoryInformation();
        this.informationService
            .setCharacteristic(Characteristic.Manufacturer, this.manufacturer)
            .setCharacteristic(Characteristic.Model, this.model)
            .setCharacteristic(Characteristic.SerialNumber, this.serial);

        // Types
        if (this.type == "contact") {
            this.securityService = new Service.ContactSensor(this.name);
            this.securityService
                .getCharacteristic(Characteristic.ContactSensorState)
                .on('get', this.getCurrentState.bind(this));
        } else if (this.type == "motion") {
            this.securityService = new Service.MotionSensor(this.name);
            this.securityService
                .getCharacteristic(Characteristic.MotionDetected)
                .on('get', this.getCurrentState.bind(this));
        } else if (this.type == "security") {
            this.securityService = new Service.SecuritySystem(this.name);
            this.securityService
                .getCharacteristic(Characteristic.SecuritySystemCurrentState)
                .on('get', this.getCurrentState.bind(this));
            this.securityService
                .getCharacteristic(Characteristic.SecuritySystemTargetState)
                .on('get', this.getTargetState.bind(this));

            this.securityService
                .getCharacteristic(Characteristic.SecuritySystemTargetState)
                .on('set', this.setTargetState.bind(this));
        }

        // Get updated values every 3 seconds
        setInterval((function() {
            var res = request(this.http_method, this.url + '/get/' + this.dpin, {});
            if (res.statusCode >= 400) {
                this.log('HTTP updateState function failed');
            } else {
                var info = JSON.parse(res.body);

                if (this.type == "security" && info.val == 4 && this.current != info.val) {
                    this.securityService.setCharacteristic(
                        Characteristic.SecuritySystemCurrentState,
                        info.val
                    );
					this.current = info.val;
                    this.log('HTTP updateState function succeeded! Data: ' + info.val);
                } else if (this.type == "contact" && this.current != info.val) {
                    this.securityService.setCharacteristic(
                        Characteristic.ContactSensorState,
                        info.val
                    );
					this.current = info.val;
                    this.log('HTTP updateState function succeeded! Data: ' + info.val);
                } else if (this.type == "motion" && this.current != info.val) {
                    this.securityService.setCharacteristic(
                        Characteristic.MotionDetected,
                        info.val
                    );
					this.current = info.val;
                    this.log('HTTP updateState function succeeded! Data: ' + info.val);
                }
            }

        }).bind(this), 3000);
        return [this.informationService, this.securityService];
    }
};