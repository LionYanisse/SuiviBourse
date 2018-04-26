cordova.define('cordova/plugin_list', function(require, exports, module) {
module.exports = [
    {
        "file": "plugins/com.red_folder.phonegap.plugin.backgroundservice/www/backgroundService.js",
        "id": "com.red_folder.phonegap.plugin.backgroundservice.BackgroundService"
    },
    {
        "file": "plugins/com.red_folder.phonegap.plugin.backgroundservice.sample/www/myService.js",
        "id": "com.red_folder.phonegap.plugin.backgroundservice.sample.MyService",
        "clobbers": [
            "cordova.plugins.myService"
        ]
    },
    {
        "file": "plugins/cordova-plugin-whitelist/whitelist.js",
        "id": "cordova-plugin-whitelist.whitelist",
        "runs": true
    },
    {
        "file": "plugins/in.edelworks.sharedpreferences/www/sharedpreferences.js",
        "id": "in.edelworks.sharedpreferences.SharedPreferences",
        "clobbers": [
            "sharedpreferences"
        ]
    }
];
module.exports.metadata = 
// TOP OF METADATA
{
    "com.red_folder.phonegap.plugin.backgroundservice": "2.0.0",
    "com.red_folder.phonegap.plugin.backgroundservice.sample": "2.0.0",
    "cordova-plugin-whitelist": "1.0.0",
    "in.edelworks.sharedpreferences": "0.1.0"
};
// BOTTOM OF METADATA
});