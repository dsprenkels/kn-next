from django.conf import settings
from django.conf.urls import patterns, include, url
from django.conf.urls.static import static
from django.shortcuts import render

from django.contrib import admin

admin.autodiscover()

urlpatterns = patterns('',
    # url(r'^geschiedenis/$', 'website.views.geschiedenis', name='geschiedenis'),
    # url(r'^bestuur/$', 'website.views.bestuur', name='bestuur'),
    # url(r'^aktanokturna/$', 'website.views.aktanokturna', name='aktanokturna'),
    # url(r'^zusjes/$', 'website.views.zusjes', name='zusjes'),
    # url(r'^sponsoren/$', 'website.views.sponsoren', name='sponsoren'),

    url(r'^admin/', include(admin.site.urls)),
    url(r'^(!(media|static)/)', include('website.urls')),
) + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT) + static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
