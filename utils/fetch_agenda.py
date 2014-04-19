#!/usr/bin/env python

# Genereert config.agenda.php van de Karpe Noktem google calendar.
#  TODO  UTF?

from cStringIO import StringIO
from datetime import datetime, timedelta
from json import dump
import locale
import re
import string
import sys
from warnings import warn

import gdata.calendar
import gdata.calendar.service
import gdata.service
from iso8601 import parse_date

try:
    # Nederlandse afkortingen voor data
    locale.setlocale(locale.LC_ALL, 'nl_NL.UTF-8')
except locale.Error:
    warn('Dutch locale not available')

default_cid = "vssp95jliss0lpr768ec9spbd8@group.calendar.google.com"

rot13 = string.maketrans( 
    "ABCDEFGHIJKLMabcdefghijklmNOPQRSTUVWXYZnopqrstuvwxyz", 
    "NOPQRSTUVWXYZnopqrstuvwxyzABCDEFGHIJKLMabcdefghijklm")

def parse_date_range(start, end):
        """ Ugly hack to properly parse gdata date ranges """
        hack_on_end = False
        if start.find('T') == -1:
                start += 'T00:00:00.000'
        if end.find('T') == -1:
                end += 'T23:59:59.000'
                hack_on_end = True
        if hack_on_end: the_end_date = parse_date(end) - timedelta(1,0,0)
        else: the_end_date = parse_date(end)
        return (parse_date(start),
                        the_end_date )

def retreive(cid):
        """ Retreives the public future events on the calendar with id <cid> """
        r = []
        now = datetime.now()
        cs = gdata.calendar.service.CalendarService()
        q = gdata.calendar.service.CalendarEventQuery(cid, 'public', 'full')
        q.start_min = "%s-%s-%s" % (now.year,
                                    str(now.month).zfill(2),
                                    str(now.day).zfill(2))
        feed = cs.CalendarQuery(q)
        for i, an_event in enumerate(feed.entry):
                if len(an_event.when) == 0:
                        continue
                r.append(
                    (an_event.title.text, an_event.content.text,)+
                    parse_date_range(an_event.when[0].start_time, an_event.when[0].end_time)
                )
        return r


def print_events(events):
        erepl = re.compile("([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})",
                           re.IGNORECASE)

        def erepl_replace(matchobj):
            email_rot13 = string.translate(matchobj.group(), rot13)
            return ('<span class="email obfuscated">%s</span>') % email_rot13

        villare = re.compile("(villa van schaeck)", re.IGNORECASE)

        # Process the events
        processed_events = []
        # Loop over the *sorted* list
        for title, content, start_date, end_date in sorted(events, key=lambda e: e[2]):
	    short_date = (start_date.strftime('%a'), str(start_date.day))
	    month = start_date.strftime('%B')
            content = "" if content is None else content.strip()
            content = content.replace("\n", "<br/>")
            content = erepl.sub(erepl_replace, content)
            content = villare.sub('<a href="https://www.google.com'
                '/maps/?q=Villa van Schaeck, Van Schaeck Mathonsingel, '
                'Nijmegen-Centrum, Nijmegen, The Netherlands">\\1</a>',
                content)
            if (end_date - start_date) < timedelta(1, 0, 0):
                start_date = start_date.strftime('%A %d %B')
                end_date = None
            else:
                start_date = (start_date.strftime('%A %%d %B') %
                        start_date.day)
                end_date = (end_date.strftime('%A %%d %B') %
                        end_date.day)
            processed_events.append({'title': title, 'description': content,
                'start_date': start_date, 'end_date': end_date,
		'short_date': short_date, 'month': month})

        # dump the events to stdout
        dump(processed_events, sys.stdout)
         

def main(cid):
        events = retreive(cid)
        print_events(events)

if __name__ == '__main__':
        if len(sys.argv) >= 2: cid = sys.argv[1]
        else: cid = default_cid
        main(cid)
