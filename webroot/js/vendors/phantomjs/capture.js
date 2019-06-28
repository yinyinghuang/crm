
var page = require('webpage').create(),
	system = require('system'),
    address,output;

page.viewportSize = { width: 990, height: 600 };
if (system.args.length < 2) {
    phantom.exit('Parameter missing');
} else {
    address = system.args[1];
    output = system.args[2];
    page.open(address, function (status) {
        if (status !== 'success') {
            console.log('Unable to load the address!');
            phantom.exit();
        } else {
            window.setTimeout(function () {
                page.render(output);
                phantom.exit();
            }, 10000);
        }
    });
}
