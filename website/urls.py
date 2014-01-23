from django.conf.urls import patterns, url

"""This file is based on django.contrib.flatpages.urls"""

urlpatterns = patterns('website.views',
    url(r'^(?P<url>.*)$', 'flatpage', name='flatpage'),
)
