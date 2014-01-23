from __future__ import unicode_literals

from django.db import models
from django.contrib.sites.models import Site
from django.core.urlresolvers import get_script_prefix
from django.utils.translation import ugettext_lazy as _
from django.utils.encoding import iri_to_uri, python_2_unicode_compatible

"""This file is based on django.contrib.flatpages.models"""

UPLOAD_TO = lambda i, fn: (('public/%s' if i.public else 'private/%s') % fn)

class WebsiteModel(models.Model):
    created = models.DateTimeField('aangemaakt',
        auto_now_add=True, editable=False)
    modified = models.DateTimeField('gewijzigd',
        auto_now=True, editable=False)
    
    class Meta:
        abstract = True

class FlatPage(WebsiteModel):
    TEMPLATE_NAME_CHOICES = (('flatpages/default.html', 'Standaard pagina'),)
    url = models.CharField('URL', max_length=100, db_index=True)
    title = models.CharField('titel', max_length=200, blank=True)
    content = models.TextField('inhoud', blank=True)
    template_name = models.CharField('template naam', max_length=70, blank=False,
        choices=TEMPLATE_NAME_CHOICES, default='default.html',
        help_text="Voorbeeld: 'flatpages/empty.html'. 'default.html' is de standaardpagina")
    public = models.BooleanField('publiek', default=True,
        help_text="Als dit niet aangevinkt is moet een gebruiker ingelogd zijn om de pagina te zien.")

    class Meta:
        verbose_name = 'pagina'
        verbose_name_plural = "pagina's"
        ordering = ('url',)

    def __str__(self):
        return "Pagina: %s -- %s" % (self.url, self.title)

    def get_absolute_url(self):
        # Handle script prefix manually because we bypass reverse()
        return iri_to_uri(get_script_prefix().rstrip('/') + self.url)

class File(WebsiteModel):
    file = models.FileField('bestand', upload_to=UPLOAD_TO)
    public = models.BooleanField('publiek', default=True,
        help_text="Als dit niet aangevinkt is moet een gebruiker ingelogd zijn"
        " om het bestand te downloaden.")

    class Meta:
        verbose_name = 'bestand'
        verbose_name_plural = 'bestanden'


class Image(WebsiteModel):
    image = models.ImageField('afbeelding', upload_to=UPLOAD_TO)
    public = models.BooleanField('publiek', default=True,
        help_text="Als dit niet aangevinkt is moet een gebruiker ingelogd zijn"
        " om de afbeelding te kunnen zien.")
    
    class Meta:
        verbose_name = 'afbeelding'
        verbose_name_plural = 'afbeeldingen'

class Redirect(WebsiteModel):
    url = models.CharField('URL', max_length=100, db_index=True)
    to = models.CharField('bestemming', max_length=100)
    permanent = models.BooleanField('permanent', default=True)

    class Meta:
        verbose_name = 'redirect'
        verbose_name_plural = 'redirects'
        ordering = ('url',)

    def __str__(self):
        if self.permanent:
            return "Pernament redirect van %s naar %s" % (self.url, self.to)
	else:
            return "Redirect van %s naar %s" % (self.url, self.to)
