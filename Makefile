BUILD=build
TEMPLATES=templates
ASSET=assets
ASSETDIRS=$(shell find assets \-mindepth 1 \-type d | sed "s/assets/build/")
BUILDASSETS=$(shell find assets \-type f | sed "s/assets/build/")
PAGES=$(BUILD)/index.html $(BUILD)/agenda.html $(BUILD)/geschiedenis.html $(BUILD)/lidworden.html $(BUILD)/contact.html

all: build assets

assets: $(ASSETDIRS) $(BUILDASSETS)

build: $(PAGES)

clean:
	rm -rf $(BUILD)

$(BUILD)/%.html: $(TEMPLATES)/%.twig $(TEMPLATES)/base.twig $(TEMPLATES)/macros.twig
	./twigcompile $*

$(ASSETDIRS):
	mkdir "$@"

$(BUILD)/%: $(ASSET)/%
	cp "$(ASSET)/$*" "$@"
