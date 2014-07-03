## Association Output for Symphony: Inline XML

When working with associated entries in Symphony, you usually need two Data Sources to fetch all your content:

- one that returns your parent entry, adding the ids of your associations to the parameter pool and
- another one that returns your associations based on these values in the pool.

While this concept is powerful, it results in related content that is spread across different nodes you have to manually match in your XSL templates. Association Output simplifies this process by providing an interface to select associated content directly in your main Data Source. Associated entries will be returned inline in your Data Source – no need to set output paramters and secondary Data Sources.

#### Performance and Caching

Under the hood, Association Output creates output parameters dynamically and uses core Data Sources to fetch the associated entries. So there shouldn't be any noticeable performance differences between the usual approach to attach associated entries and this one. Please keep in mind though that returning a few thousand associations in your XML will result in declined performance, as usual.

Caching should work as with any other Data Source.

#### Field Compatibility

Association Output is compatible with all fields using Symphony's core association system:

- select boxes (core)
- tag lists (core)
- [Association Field](https://github.com/symphonists/association_field)
- and others

### Acknowledgement

This project has kindly been funded by [Bernardo Dias da Cruz](http://bernardodiasdacruz.com/), Ben Babcock, Juraj Kapsz, Daniel Golbig, Vojtech Grec, [Andrea Buran](http://www.andreaburan.com/), [Brendan Abbot](http://bloodbone.ws/), [Roman Klein](http://romanklein.com), [Korelogic](http://korelogic.co.uk/), Ngai Kam Wing, [David Oliver](http://doliver.co.uk/), Patrick Probst, Mario Butera, John Puddephatt, [Goldwiege](http://www.goldwiege.de/), Andrew Minton, munki-boy, Martijn Kremers, Ian Young, Leo Nikkilä, [Jonathan Mifsud](http://jonmifsud.com/) and others. [Read more](http://www.getsymphony.com/discuss/thread/106489/).

If you like this extension, please consider a donation to support the further development.

[![PayPal Donation](https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YAVPERDXP89TC)
