from django import forms
from django.conf import settings

from website.models import FlatPage, Redirect

"""Deze module is gebaseerd op django.contrib.flatpages.forms"""

class FlatpageForm(forms.ModelForm):
    url = forms.RegexField(label="URL", max_length=100, regex=r'^[-\w/\.~]+$',
        help_text = "Voorbeeld: '/geschiedenis/'. Vergeet niet aan het begin"
                          " en aan het eind een slash ('/') te doen.",
        error_message = "Dit veld mag alleen letters, cijfers, punten,"
                      " underscores, koppeltekens, slashes en tildes bevatten")

    class Meta:
        model = FlatPage
        fields = '__all__'

    def clean_url(self):
        url = self.cleaned_data['url']
        if not url.startswith('/'):
            raise forms.ValidationError(
                "URL is mist een slash aan het begin",
                code='missing_leading_slash',
            )
        if (settings.APPEND_SLASH and
            'django.middleware.common.CommonMiddleware' in settings.MIDDLEWARE_CLASSES and
            not url.endswith('/')):
            raise forms.ValidationError(
                "URL mist een slash aan het eind.",
                code='missing_trailing_slash',
            )
        return url

    def clean(self):
        url = self.cleaned_data.get('url', None)
        sites = self.cleaned_data.get('sites', None)

        same_url = FlatPage.objects.filter(url=url)
        # filter mijzelf eruit
        if self.instance.pk:
            same_url = same_url.exclude(pk=self.instance.pk)

        if same_url.exists():
	    raise forms.ValidationError(
		'Pagina met URL %s bestaat al' % url,
		code='duplicate_url',
		params={'url': url},
	    )

        if Redirect.objects.filter(url=url).exists():
            raise forms.ValidationError(
                'Er bestaat al een redirect met de URL %s' % url,
                code='duplicate_url_with_redirect',
                params={'url': url},
            )

        return super(FlatpageForm, self).clean()
