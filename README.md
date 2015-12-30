kn-next
=======

## This repository is not used anymore. The code has been merged into [kninfra](https://github.com/karpenoktem/kninfra).

Clone the repository into a PHP-enabled webserver.
You'll have to load the agenda with a CRON-script, or for testing by hand:

`python utils/fetch_agenda.py >/tmp/kn-next_agenda.json`

Also, kn-next needs rewrite-rules, so that `/over` will be redirected to
`/index.php?action=over`.

In Lighttpd, you can use these rules to accomplish this:

    url.rewrite += (
        # Do not redirect these urls
        "^/$" => "/kn-next/index.php?action=index",
        "^/(css|js|img|aktas)(?:/(.*))?$" => "/kn-next/$0",
        # Redirect the rest to the action
        "^/admin$" => "/kn-next/admin.php",
        "^/(.*)$" => "/kn-next/index.php?action=$1"
    )
