! function(e) {
    SlideDeckSkin["o-town"] = function(a) {
        var t = "o-town",
            i = e(a).slidedeck(),
            s = i.vertical(),
            n = {};
        n.slidedeck = i.deck, n.frame = n.slidedeck.closest(".lens-" + t), n.horizontalSlides = i.slides, n.verticalSlides = s.slides, n.indexIndicators = n.frame.find(".slidedeck-index-indicator span.total"), n.indexIndicators.html(n.verticalSlides.length), n.currentindexIndicators = n.frame.find(".slidedeck-index-indicator span.current"), n.verticalSlides.each(function(a) {
            e(n.currentindexIndicators[a]).html(a + 1)
        }), s.options.scroll = i.options.scroll ? !0 : !1;

	


        var r = n.frame.hasClass("sd2-nav-thumb"),
	
            l = {};
		
	
        l.paged = !1, r || 8 >= slidedeck_ie && n.verticalSlides.each(function(a) {
            if ("none" != e(n.verticalSlides[a]).css("background-image")) {
                var t = e(n.verticalSlides[a]).css("background-image").match(/url\([\"\'](.*)[\"\']\)/)[1];
                e(n.verticalSlides[a]).css({
                    background: "none"
                }), n.verticalSlides[a].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + t + "', sizingMethod='scale')"
            }
        }), l.updateSetNavigation = function() {
            l.deckSetNav.removeClass("disabled"), l.currentSetIndex ? l.currentSetIndex + 1 == l.sets && l.deckSetNav.not(".prev").addClass("disabled") : l.deckSetNav.not(".next").addClass("disabled")
        }, n.slidedeck.find(".sd2-slide-title").removeClass("accent-color"), n.slidedeck.find(".sd2-slide-text a").addClass("accent-color"), n.frame.hasClass("content-source-custom") ? n.slidedeck.find(".sd2-node-caption .play-video-alternative").addClass("accent-color") : n.slidedeck.find(".sd2-node-caption .play-video-alternative").addClass("accent-color-background").removeClass("accent-color"), n.slidedeck.slidedeck().loaded(function() {
            var t = n.frame.hasClass("sd2-nav-dots"),
                c = i.options.autoPlay,
                d = n.frame.hasClass("sd2-autoplay-snake"),
                v = n.frame.hasClass("sd2-autoplay-hide");
            c && !e(a).slidedeck().pauseAutoPlay && n.frame.addClass("auto-play"), l.currentVertSlideIndex = s.current, e(s.slides[l.currentVertSlideIndex]).addClass("active"), l.decknavigation = n.frame.find(".deck-navigation.vertical"), n.frame.append('<div class="vertical-slide-nav-wrapper"><div class="vertical-slide-nav-elems-wrapper"></div></div>'), l.verticalNavWrapper = n.frame.find("div.vertical-slide-nav-wrapper"), l.verticalNavElemsWrapper = n.frame.find("div.vertical-slide-nav-elems-wrapper"), r || l.verticalNavWrapper.append('<span class="vertical-slide-nav-shadow">&nbsp;</span>');
            for (var o = 0; o < n.verticalSlides.length; o++) {
                var p = o + 1;
                l.verticalNavElemsWrapper.append('<span class="vertical-slide-nav"><a href="#' + p + '">' + p + "</a></span>")
            }
            if (l.verticalNavElems = l.verticalNavElemsWrapper.find("span.vertical-slide-nav"), e(l.verticalNavElems[l.currentVertSlideIndex]).addClass("active"), l.verticalNavLinks = l.verticalNavElemsWrapper.find("a"), l.verticalNavItemHeight = l.verticalNavElems.outerHeight(!0), !r)
                for (var o = 0; o < n.verticalSlides.length; o++) {
                    var f = e("<span></span>").addClass("vertical-slide-nav-background").css({
                        position: "absolute",
                        top: 0,
                        left: 0,
                        right: 0,
                        height: l.verticalNavItemHeight,
                        overflow: "hidden",
                        zIndex: 5
                    }).appendTo(e(l.verticalNavElems[o]));
                    e('<span class="inner">&nbsp;</span>').css({
                        height: Math.round(2 * l.verticalNavItemHeight),
                        marginTop: -1 * Math.round(l.verticalNavItemHeight)
                    }).appendTo(f)
                }
            if (l.verticalNavLinks.each(function(i) {
                    var s;
                    if (s = r ? '<span class="slide-nav-inner">' + (i + 1) + "</span>" : i + 1, t && (s = '<span class="dot-inner">' + s + "</span>"), e(this).html(s), c && !e(a).slidedeck().pauseAutoPlay && !v) {
                        var n = '<span class="auto-play-indicator accent-color-background">&nbsp;</span>';
                        d && (n = '<span class="auto-play-indicator snake-1 accent-color-background">&nbsp;</span>', n += '<span class="auto-play-indicator snake-2 accent-color-background">&nbsp;</span>', n += '<span class="auto-play-indicator snake-3 accent-color-background">&nbsp;</span>', n += '<span class="auto-play-indicator snake-4 accent-color-background">&nbsp;</span>'), e(this).append(n)
                    }
                }), r) {
                if (l.verticalNavElemsWrapper.height() < 110) return n.frame.find(".vertical-slide-nav-wrapper").hide(), n.verticalSlides.each(function() {
                    e(this).find(".slide-content, .sd2-slide-background").css({
                        left: 0
                    })
                }), !1;
                l.verticalNavLinksInner = l.verticalNavLinks.find("span.slide-nav-inner");
		jQuery('.slide-nav-inner').parent().addClass('addthumb');
                var u = new Array;
                n.verticalSlides.each(function(e) {
                    var a = n.verticalSlides.eq(e);
                    u[e] = a.attr("data-thumbnail-src");
                    var t = a.find(".sd2-slide-background"),
                        i = t.css("background-image");
                    if (void 0 == i) l.verticalNavLinksInner.eq(e).addClass("no-thumb");
                    else if (8 >= slidedeck_ie) {
                        if ("none" != i) {
                            var s = i.match(/url\([\"\'](.*)[\"\']\)/)[1];
                            l.verticalNavLinksInner.eq(e).css({
                                background: "none"
                            }), l.verticalNavLinksInner[e].style.filter = u[e] ? "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + u[e] + "', sizingMethod='scale')" : "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + s + "', sizingMethod='scale')", t.css({
                                background: "none"
                            }), t[0].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + s + "', sizingMethod='scale')"
                        }
                    } else u[e] = u[e] ? u[e] : i, "none" != u[e] && l.verticalNavLinksInner.eq(e).css({
                        "background-image": 'url("' + u[e] + '")'
                    })
                })
            }

            if (l.verticalNavElemsWrapper.height() < l.verticalNavItemHeight * l.verticalNavElems.length) {
                l.paged = !0, r ? (l.deckSetNavHeight = 52, n.frame.hasClass("sd2-small") && (l.deckSetNavHeight = 31)) : l.deckSetNavHeight = 25, l.verticalNavElemsWrapperTop = l.verticalNavElemsWrapper.position().top, l.verticalNavElemsWrapperWidth = l.verticalNavElemsWrapper.width(), l.verticalNavElemsWrapper.css({
                    top: l.verticalNavElemsWrapperTop + l.deckSetNavHeight,
                  bottom: parseInt(l.verticalNavElemsWrapper.css("bottom").replace("px", "")) + l.deckSetNavHeight

                }), n.frame.append('<a href="#prev-set" class="deck-set-nav prev"><span class="inner">Prev</span></a><a href="#next-set" class="deck-set-nav next"><span class="inner">Next</span></a>'), l.deckSetNav = n.frame.find("a.deck-set-nav"), c && (l.verticalNavElemsWrapperTop = l.verticalNavElemsWrapperTop), r ? (l.deckSetNavOffset = 11, n.frame.hasClass("sd2-small") && (l.deckSetNavOffset = 1), e(l.deckSetNav[0]).css({
                    top: l.deckSetNavOffset
                }), e(l.deckSetNav[1]).css({
                    bottom: l.deckSetNavOffset
                })) : (e(l.deckSetNav[0]).css({
                    top: l.verticalNavElemsWrapperTop,
                    paddingTop: l.deckSetNavHeight,
                    width: l.verticalNavElemsWrapperWidth
                }), e(l.deckSetNav[1]).css({
                    bottom: 0,
                    paddingTop: l.deckSetNavHeight,
                    width: l.verticalNavElemsWrapperWidth
                })), l.verticalNavElemsWrapperHeight = l.verticalNavElemsWrapper.height(), l.navsPerSet = Math.floor(l.verticalNavElemsWrapperHeight / l.verticalNavItemHeight), l.sets = Math.ceil(l.verticalNavElems.length / l.navsPerSet), l.verticalNavElems.wrapAll('<div class="vertical-slide-nav-wrapper-inner"></div>'), l.navWrapperInner = l.verticalNavElemsWrapper.find(".vertical-slide-nav-wrapper-inner"), l.navWrapperInner.css({
                    position: "absolute",
                    top: 0,
                    right: 0,
                    left: 0
                });
                for (var h = 0, m = 1, k = '<div class="vertical-slide-nav-set"></div>', N = 0; N < l.sets; N++) {
                    var g = l.verticalNavElems.slice(h, l.navsPerSet * m);
                    g.wrapAll(k), h += l.navsPerSet, m++
                }
                l.currentSetIndex = 0, l.navSets = l.verticalNavElemsWrapper.find(".vertical-slide-nav-set");
                var S = l.verticalNavItemHeight * l.navsPerSet;
			
                l.verticalNavElemsWrapper.css({
                    height: "auto",
                    top: S /2 +"px",
                    marginTop: S / 2 * -1
                }), l.activeNavParent = l.verticalNavElemsWrapper.find(".vertical-slide-nav.active").parent("div"), l.activeNavParent.addClass("active"), l.deckSetNav.bind("click", function(a) {
                    if (a.preventDefault(), e(this).hasClass("next")) {
                        if (l.currentSetIndex + 1 == l.sets) return !1;
                        l.currentSetIndex = l.currentSetIndex + 1, l.navWrapperInner.animate({
                            top: -1 * e(l.navSets[l.currentSetIndex]).position().top
                        }, 500)
                    } else if (e(this).hasClass("prev")) {
                        if (0 == l.currentSetIndex) return !1;
                        l.currentSetIndex = l.currentSetIndex - 1, l.navWrapperInner.animate({
                            top: -1 * e(l.navSets[l.currentSetIndex]).position().top
                        }, 500)
                    }
                    l.updateSetNavigation()
                }), l.updateSetNavigation()
            } else if (r) {
                var S = l.verticalNavItemHeight * l.verticalNavElems.length;
                l.verticalNavElemsWrapper.css({
                    height: "auto",
                    top: "50%",
                    marginTop: S / 2 * -1
                })
            }
            var I = function(a) {
                    l.verticalNavElems.removeClass("active"), e(l.verticalNavElems[a]).addClass("active"), l.paged && (l.activeNavParent = l.verticalNavElemsWrapper.find(".vertical-slide-nav.active").parent("div"), l.activeNavParent.hasClass("active") || (l.navSets.removeClass("active"), l.activeNavParent.addClass("active"), l.currentSetIndex = l.navSets.index(l.activeNavParent), l.navWrapperInner.animate({
                        top: -1 * l.activeNavParent.position().top
                    }, 500)), l.updateSetNavigation())
                },
                b = function() {
                    l.verticalNavLinks.find("span.auto-play-indicator").hide(), c = !1, e(a).slidedeck().pauseAutoPlay = !0
                };
            l.decknavigation.bind("click", function(t) {
                t.preventDefault(), c && !e(a).slidedeck().pauseAutoPlay && b();
                var s;
                s = e(this).hasClass("prev") ? 0 == i.vertical().current ? -1 : i.vertical().current - 1 : i.vertical().current == i.vertical().slides.length - 1 ? -1 : i.vertical().current + 1, -1 != s && I(s)
            }), l.verticalNavLinks.bind("click", function(t) {
                t.preventDefault(), c && !e(a).slidedeck().pauseAutoPlay && b();
                var s = l.verticalNavLinks.index(e(this));
                l.verticalNavLinks.parents("span.vertical-slide-nav").removeClass("active"), e(this).parents("span.vertical-slide-nav").addClass("active"), i.pauseAutoPlay = !0, i.vertical().goTo(s + 1)
            });
            var E = function(a) {
                    if (!v) {
                        var t = e(l.verticalNavLinks[a]),
                            s = "linear";
                        if (d) {
                            var n = i.options.autoPlayInterval / 4,
                                r = t.find("span.snake-1"),
                                c = t.find("span.snake-2"),
                                o = t.find("span.snake-3"),
                                p = t.find("span.snake-4");
                            r.animate({
                                width: "100%"
                            }, n, s, function() {
                                c.animate({
                                    height: "100%"
                                }, n, s, function() {
                                    o.animate({
                                        width: "100%"
                                    }, n, s, function() {
                                        p.animate({
                                            height: "100%"
                                        }, n, s, function() {
                                            r.add(o).css("width", 0), c.add(p).css("height", 0)
                                        })
                                    })
                                })
                            })
                        } else t.find("span.auto-play-indicator").animate({
                            width: "100%"
                        }, i.options.autoPlayInterval, s, function() {
                            e(this).css("width", 0)
                        })
                    }
                },
                W = s.options.before;
            s.options.before = function(t) {
                if ("function" == typeof W && W(t), c && !e(a).slidedeck().pauseAutoPlay) {
                    var i = t.current;
                    t.slides.length == i && (i = 0), E(i), I(i)
                }
            };
            var y = s.options.complete;
            s.options.complete = function(e) {
                "function" == typeof y && y(e), I(e.current)
            }, c && !e(a).slidedeck().pauseAutoPlay && E(l.currentVertSlideIndex);
            var x = !0;
            "undefined" != typeof e.event.special.mousewheel && n.frame.bind("mousewheel", function(e, a) {
                s.options.scroll && (x && (b(), x = !0), 1 == a ? I(i.vertical().current) : -1 == a && I(i.vertical().current))
            }), v && l.verticalNavLinks.find("span.auto-play-indicator").hide()
        })
    }, e(document).ready(function() {
        e(".lens-o-town .slidedeck").each(function() {
            ("undefined" == typeof e.data(this, "lens-o-town") || null == e.data(this, "lens-o-town")) && e.data(this, "lens-o-town", new SlideDeckSkin["o-town"](this))
        })
    })
}(jQuery);
