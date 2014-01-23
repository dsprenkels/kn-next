from django.contrib import admin
from django.utils.translation import ugettext_lazy as _
from django.utils.functional import curry

from website.forms import FlatpageForm
from website.models import FlatPage, File, Image, Redirect

@curry(admin.site.register, FlatPage)
class FlatPageAdmin(admin.ModelAdmin):
    form = FlatpageForm
    fieldsets = (
        (None, {'fields': ('url', 'title', 'content')}),
        ('Geavanceerde opties', {'classes': ('collapse',), 'fields': ('public', 'template_name')}),
    )
    list_display = ('url', 'title', 'created', 'modified', 'public')
    list_filter = ('public',)
    search_fields = ('url', 'title')

@curry(admin.site.register, Redirect)
class RedirectAdmin(admin.ModelAdmin):
    list_display = ('url', 'to', 'created', 'modified')
    search_fields = ('url', 'to')

@curry(admin.site.register, File)
class FileAdmin(admin.ModelAdmin):
    list_display = ('file', 'created', 'modified', 'public')
    search_fields = ('url',)

@curry(admin.site.register, Image)
class ImageAdmin(admin.ModelAdmin):
    list_display = ('image', 'created', 'modified', 'public')
    search_field = ('image',)
