# simple-php-mailchimp-api-v3

A simple PHP API for Mailchimp version 3.

Initialize:
`$mc = new mailchimp\API3('YOUR_API');`

Make a GET call
`$campaigns = $mc->call('get', 'campaigns', array('count' => 20));`

Make a POST call 
`$campaigns = $mc->call('post', 'campaigns-folders', array('name' => 'Easter campaigns'));`

Make a DELETE call
`$campaigns = $mc->call('delete', 'campaigns', array('campaign_id' => '21442'));`

Full list of Mailchimp API v3 endpoints and documentation can be seen here. http://developer.mailchimp.com/documentation/mailchimp/reference/overview/

Doesn't yet support PUT and HEAD methods.
