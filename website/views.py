from django.conf import settings
from django.contrib.auth.views import redirect_to_login
from django.http import Http404, HttpResponse, HttpResponseRedirect, \
        HttpResponsePermanentRedirect
from django.shortcuts import get_object_or_404
from django.template import loader, RequestContext
from django.utils.functional import lazy
from django.utils.html import format_html
from django.utils.safestring import mark_safe
from django.views.decorators.csrf import csrf_protect

from website.models import FlatPage, Redirect

"""This module is based on django.contrib.flatpages.views"""

def flatpage(request, url):
    """
    View voor het handlen van "flat pages"

    Gebruikt het FlatPage-model
    """
    if not url.startswith('/'):
        url = '/' + url
    try:
        f = get_object_or_404(FlatPage, url__exact=url)
    except Http404:
        if not url.endswith('/') and settings.APPEND_SLASH:
            url += '/'
            f = get_object_or_404(FlatPage, url__exact=url)
            return HttpResponsePermanentRedirect('%s/' % request.path)
        else:
	    return redirect(request, url)
    return render_flatpage(request, f)

def redirect(request, url):
    """
    View voor het afhandelen van redirects

    Gebruikt het Redirect-models
    """
    if not url.startswith('/'):
        url = '/' + url
    try:
        redirect = get_object_or_404(Redirect, url__exact=url)
    except Http404:
        if not url.endswith('/') and settings.APPEND_SLASH:
            url += '/'
            redirect = get_object_or_404(Redirect, url__exact=url)
        else:
	    raise
    HttpResponseRedirectClass = HttpResponsePermanentRedirect \
        if redirect.permanent else HttpResponseRedirect
    return HttpResponseRedirectClass(redirect.to)


# Below here are only helper-functions

@csrf_protect
def render_flatpage(request, f):
    """
    Internal interface to the flat page view.
    """
    # Check of ingelogd zijn nodig is
    if not f.public and not request.user.is_authenticated():
        return redirect_to_login(request.path)
    
    t = loader.get_template(f.template_name)

    # Markeer title en content als safe, zodat we geen '|safe' hoeven hoeven te 
    # gebruiken. Het is allemaal plain HTML anyway
    f.title = mark_safe(f.title)
    f.content = mark_safe(f.content)

    c = RequestContext(request, {
        'flatpage': _process_flatpage(f),
    })
    response = HttpResponse(t.render(c))
    return response

def _process_flatpage(f):
    """
    Vervang tekst zoals {AGENDA} of {SLIDESHOW} met de corresponderende stukjes 
    tekst. Gebruik indien nodig lazy implementaties van de functies, zodat er 
    het genereren van code niet onnodig gebeurt.
    """
    f.content = format_html(f.content,
        AGENDA=mark_safe(lazy_agenda()), SLIDESHOW=mark_safe(lazy_slideshow()),
        MEDIA_URL=settings.MEDIA_URL
    )
    return f

def agenda():
    """Geeft de HTML terug voor de agenda"""
    raise NotImplementedError
lazy_agenda = lazy(agenda, unicode)

def slideshow():
    """Geeft de HTML terug voor de slideshow"""
    raise NotImplementedError
lazy_slideshow = lazy(slideshow, unicode)

