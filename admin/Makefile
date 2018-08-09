# Makefile for gettext locale support.
# 
# Targets:
#   make		-- update, merge and compile *.po and *.pot files
#   make new-locale	-- generate *.po for new locale
# 
# If "make new-locale" fails, try create locale direct from command line:
# 
#   bash$> mkdir -p locale/sv_SE.UTF-8/LC_MESSAGES
#   bash$> msginit --input=locale/messages.pot \
#		   --output=locale/sv_SE.UTF-8/LC_MESSAGES/messages.po \
#		   --locale=sv_SE.UTF-8
# 
# Enable po-mode in jed/emacs or install the poedit for editing *.po files.
# 
# Author: Anders Lövgren
# Date:   2018-08-09

PACKAGE_NAME    = sitename
PACKAGE_VERSION = 1.0
PACKAGE_ADDRESS = contact@example.com
PACKAGE_COPYING = "Copyright notice for this application or site"

SOURCES  = $(shell find public template -type f -name '*.inc' -o -name '*.php' -o -name '*.menu' -o -name '*.ui')

XGETTEXT = xgettext
MSGMERGE = msgmerge
MSGFMT   = msgfmt
MSGCONV  = msgconv

XGETTEXT_OPTIONS = --language=PHP --package-name=$(PACKAGE_NAME) --package-version=$(PACKAGE_VERSION) --msgid-bugs-address=$(PACKAGE_ADDRESS) --copyright-holder=$(PACKAGE_COPYING) --add-comments='//{tr}' --from-code=UTF-8 --no-wrap --output=$(GETTEXT_POTFILE)
MSGMERGE_OPTIONS = --no-wrap --update
MSGFMT_OPTIONS   = --statistics --check --output-file=$@
MSGCONV_OPTIONS  = --no-wrap --to-code=utf-8

GETTEXT_POTFILE = locale/messages.pot
GETTEXT_POFILES = $(shell find locale -type f -name *.po)
GETTEXT_MOFILES = $(GETTEXT_POFILES:.po=.mo)

.PHONY : all clean all-clean distclean install gettext gettext-update gettext-merge gettext-compile

all : gettext
gettext : gettext-update gettext-merge gettext-compile
	
new-locale :
	@( \
	read -p "Locale: " locale; \
	mkdir -p locale/$$locale/LC_MESSAGES; \
	msginit --input=locale/messages.pot \
		--output=locale/$$locale/LC_MESSAGES/messages.po \
		--locale=$$locale \
	)
	
gettext-update : $(GETTEXT_POTFILE)
$(GETTEXT_POTFILE) : $(SOURCES)
	$(XGETTEXT) $(XGETTEXT_OPTIONS) $(SOURCES)
gettext-merge : $(GETTEXT_POFILES)
$(GETTEXT_POFILES) : $(GETTEXT_POTFILE)
	$(MSGMERGE) $(MSGMERGE_OPTIONS) $@ $? && touch $@
gettext-compile : gettext-merge $(GETTEXT_MOFILES)

%.mo : %.po
	$(MSGFMT) $(MSGFMT_OPTIONS) $+

gettext-format : $(GETTEXT_POFILES)
	$(MSGCONV) $(MSGCONV_OPTIONS) $+ -o $+

install :
	@(echo "Sorry, no install exists. See file INSTALL")

clean :
	rm -f $(GETTEXT_MOFILES)
all-clean : clean
	find . | grep '~' | xargs rm -f
distclean : all-clean
