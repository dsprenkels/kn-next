BUILD=build
TEMPLATES=templates
ASSET=assets
ASSETDIRS=$(shell find assets \-mindepth 1 \-type d | sed "s/assets/build/")
SCRIPTDIR=scripts
SCRIPTS=$(BUILD)/admin.php
BUILDASSETS=$(shell find assets \-type f | sed "s/assets/build/")
PAGES=$(BUILD)/index.html \
	  $(BUILD)/agenda.html \
      $(BUILD)/over.html \
	  $(BUILD)/geschiedenis.html \
	  $(BUILD)/activiteiten.html \
	  $(BUILD)/bestuur.html \
	  $(BUILD)/aktanokturna.html \
	  $(BUILD)/zusjes.html \
	  $(BUILD)/sponsoren.html \
	  $(BUILD)/lidworden.html \
	  $(BUILD)/contact.html
SLIDESHOW=slideshow
SLIDESHOW_FILES=$(shell find slideshow \-maxdepth 1 \-type f | sed "s/slideshow/build\/img\/slideshow/")

all: build assets slideshow
	cp "$(SCRIPTDIR)/admin.php" $(BUILD)/admin.php
	cp -r templates $(BUILD)/templates

assets: $(ASSETDIRS) $(BUILDASSETS)

build: $(PAGES)

slideshow: $(SLIDESHOW_FILES)
	./twigcompile index

display:
	echo $(SLIDESHOW_FILES)

clean:
	rm -rf $(BUILD)

$(BUILD)/%.html: $(TEMPLATES)/%.twig $(TEMPLATES)/base.twig $(TEMPLATES)/macros.twig
	./twigcompile $*

$(ASSETDIRS):
	mkdir "$@"

$(BUILD)/%: $(ASSET)/%
	cp "$(ASSET)/$*" "$@"

$(BUILD)/img/slideshow/%: $(ASSETDIRS) slideshowdir $(SLIDESHOW)/%
	cp "$(SLIDESHOW)/$*" "$@"

slideshowdir:
	if [ -d $(BUILD)/img/$(SLIDESHOW) ]; then \
		rm -r $(BUILD)/img/$(SLIDESHOW); \
	fi
	mkdir $(BUILD)/img/$(SLIDESHOW)