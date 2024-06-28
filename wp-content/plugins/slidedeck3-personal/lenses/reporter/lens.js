! function(a) {
    SlideDeckLens.reporter = function(n) {
        var e = this,
            n = a(n),
            t = n.closest(".slidedeck-frame"),
            s = t.find("img.slide-image"),
            d = n.slidedeck(),
            o = n,
            r = !1,
            c = !1,
            l = !1,
            h = (t.find(".accent-color").css("color"), 0),
            v = 1;
        d.loaded(function() {
            o.find(".cover .play").click(function() {
                a(this).parents("dd").addClass("show-video-wrapper"), a(this).parents("dd").addClass("hide-slide-content")
            }), a(".slide-type-video .play-video, .slide-type-video .play-video-alternative").bind("click", function(n) {
                n.preventDefault();
                var e = a(this).parents("dd"),
                    t = e.find(".video-wrapper .cover .play-video-button");
                t.trigger("click")
            }), t.hasClass("content-source-custom") || t.find("dd.slide").eq(d.current - 1).hasClass("no-image") && t.find(".dot-nav").css("margin-left", -(t.find(".dot-nav").outerWidth() / 2)).addClass("no-image")
        }), this.widthOrHeight = function() {
            var n = this;
            s.one("load", function(e) {
                var t = a(e.target);
                n.adjustImageHeight(t)
            }).each(function() {
                "" != this.src && this.complete && a(this).load()
            })
        }, this.adjustImageHeight = function(a) {
            var n = (a.attr("src"), a.width()),
                e = (a.outerWidth(), a.height()),
                t = a.outerHeight(),
                s = (a.parents("div.image").width(), a.parents("div.image").height());
            return parseInt(n) < 1 || parseInt(e) < 1 ? !1 : void(t > s && a.css({
                width: "auto",
                height: s
            }))
        }, this.dotNavigation = function() {
            if (t.hasClass("sd2-nav-dots")) {
                var e = 20;
                t.hasClass("sd2-small") && (e = 10);
                var s = n.find("dd.slide").length,
                    o = '<ul class="dot-nav"></ul>';
                a(o).appendTo(t);
                var r = t.find(".dot-nav");
                for (i = 0; i < Math.min(s, e); i++) a("<li></li>").appendTo(r);
                var c = r.find("li");
                r.css("width", Math.min(s, e) * (c.outerWidth() + 10) - 10), r.css("margin-left", -(r.outerWidth() / 2)), c.eq(d.current - 1).addClass("accent-color-background"), c.bind("click", function() {
                    var n = a(this);
                    d.goTo(n.index() + 1), c.removeClass("accent-color-background"), n.addClass("accent-color-background")
                })
            }
        }, this.syncButtonNavigation = function() {
            if (c && !l) {
                var a = Math.ceil(d.current / h);
                c.goTo(a)
            }
        }, this.positionPlayButtons = function() {
            t.find(".image .play-video-alternative").each(function() {
                var n = a(this);
                n.css({
                    "margin-top": "-" + Math.round(parseInt(n.css("padding-top")) / 2) + "px",
                    "margin-left": "-" + Math.round(n.width() / 2) + "px"
                }), n.append('<span class="play-icon"></span>');
                var e = n.find(".play-icon"),
                    t = e.width(),
                    s = e.height(),
                    i = Raphael(e[0], t, s),
                    d = i.circle(Math.round(t / 2), Math.round(s / 2), Math.round(.48 * t)),
                    o = {
                        normal: .8,
                        hover: 1
                    },
                    r = "M" + .35 * t + "," + .25 * s;
                r += "L" + .75 * t + "," + s / 2, r += "L" + .35 * t + "," + .75 * s, r += "z";
                var c = i.path(r);
                c.attr({
                    stroke: "none",
                    fill: "rgba(0,0,0,1)"
                }), d.attr({
                    stroke: "none",
                    fill: "rgba(255,255,255," + o.normal + ")"
                }), n.bind("mouseenter", function() {
                    d.attr({
                        fill: "rgba(255,255,255," + o.hover + ")"
                    })
                }), n.bind("mouseleave", function() {
                    d.attr({
                        fill: "rgba(255,255,255," + o.normal + ")"
                    })
                })
            })
        }, this.buttonNavigation = function() {
            
            if (t.hasClass("sd2-nav-titles") || t.hasClass("sd2-nav-dates")) {
                var s = n.find("dd.slide").length,
                    o = n.find("dd.slide .nav-button"),
                    f = '<div class="button-nav"></div>',
                    u = a(f).appendTo(t),
                    g = parseInt(u.outerWidth()),
                    m = 3.5,
                    k = 100 - 2 * m;
                h = Math.ceil(t.hasClass("sd2-nav-dates") ? g / 140 : g / 160), v = Math.ceil(s / h), t.hasClass("sd2-small") && (h = Math.ceil(g / 100)), h >= s && (m = !1, k = 100);
                var b = t.find(".button-nav");
                m && b.append('<a class="nav-arrow prev" href="#prev-page" style="width:' + m + '%;"></a>');
                var C = 0;
                
                for (b.append('<dl class="nav-slidedeck" style="width:' + k + '%;"></dl>'), r = t.find("dl.nav-slidedeck"), p = 1; v >= p; p++) {
                    r.append('<dd class="page"></dd>');
                    var y = t.find("dl.nav-slidedeck dd:eq(" + (p - 1) + ")");
                  
                for (i = C; i < Math.min(s, h) * p; i++) {
                        var w = o[C];
                        a(w).find(".sd2-nav-title").append('<span class="icon-caret"></span>'), w ? a(o[C]).appendTo(y) : a('<span class="spacer"></span>').appendTo(y), C++
                    }
                    
                }
                
                if (c = t.find("dl.nav-slidedeck").slidedeck({
                        keys: !1,
                        scroll: !1,
                        cycle: n.slidedeck().options.cycle
                    }), m && b.append('<a class="nav-arrow next" href="#next-page" style="width:' + m + '%;"></a>'), t.hasClass("sd2-nav-dates")) {
                    var M = "background";
                    t.hasClass("sd2-transparent-background") && (M = "accent-color-background"), o.eq(d.current - 1).find("span").addClass(M)
                } else o.eq(d.current - 1).addClass("active");
                o.bind("click", function() {
                    var n = a(this);
                    d.goTo(o.index(this) + 1), t.hasClass("sd2-nav-dates") ? o.find("span").removeClass("active") : o.removeClass("active"), t.hasClass("sd2-nav-dates") ? n.find("span").addClass("active") : n.addClass("active")
                }), o.css({
                    width: 100 / Math.min(s, h) + "%"
                }), r.find("span.spacer").css({
                    width: 100 / Math.min(s, h) + "%"
                }), t.find(".button-nav").bind("mouseenter mouseleave", function(a) {
                    l = "mouseenter" == a.type ? !0 : !1
                }), t.find(".button-nav .nav-arrow").each(function() {
                    var n = a(this);
                    n.append('<span class="icon-shape-prev-next"></span>');
                    var e = n.find(".icon-shape-prev-next"),
                        s = e.width(),
                        i = e.height(),
                        d = 2,
                        o = "#ffffff",
                        r = Raphael(e[0], s, i),
                        c = "M0,0";
                    c += "L" + d + ",0", c += "L" + (s - d) + "," + i / 2, c += "L" + d + "," + i, c += "L0," + i, c += "L" + (s - 2 * d) + "," + i / 2, c += "z";
                    var l = r.path(c);
                    "#prev-page" == this.hash && l.transform("s-1,1"), t.hasClass("sd2-light") && (o = "#333333"), l.attr({
                        stroke: "none",
                        fill: o
                    }), e.data("prev-next-arrows", l)
                }), t.find(".button-nav .nav-arrow").bind("click", function(a) {
                    switch (a.preventDefault(), this.hash) {
                        case "#prev-page":
                            c.prev();
                            break;
                        case "#next-page":
                            c.next()
                    }
                }), e.syncButtonNavigation()
            }
            
        }, this.hijackClickOnVideoThumb = function() {
            t.find(".slide-type-video a.sd2-image-link").click(function(a) {
                a.preventDefault()
            })
        };
        var f = d.options.complete;
        d.setOption("complete", function() {
            "function" == typeof f && f(d)
        });
        var u = d.options.before;
        return d.setOption("before", function(a) {
            if ("function" == typeof u && u(a), t.hasClass("sd2-nav-dots")) {
                var n = t.find(".dot-nav").find("li");
                n.removeClass("accent-color-background"), n.eq(a.current - 1).addClass("accent-color-background")
            }
            var s = t.find(".button-nav").find(".nav-button");
            if (t.hasClass("sd2-nav-dates")) {
                var i = "background";
                t.hasClass("sd2-transparent-background") && (i = "accent-color-background"), s.find("span").removeClass(i), s.eq(a.current - 1).find("span").addClass(i)
            }
            t.hasClass("sd2-nav-titles") && (s.removeClass("active"), s.eq(a.current - 1).addClass("active")), e.syncButtonNavigation();
            var d = a.slides.eq(a.current - 1).find("img.slide-image");
            e.adjustImageHeight(d)
        }), this.hijackClickOnVideoThumb(), this.positionPlayButtons(), this.dotNavigation(), this.buttonNavigation(), this.widthOrHeight(), !0
    }, a(document).ready(function() {
        a(".lens-reporter .slidedeck").each(function() {
            "undefined" == typeof a.data(this, "lens-reporter") && a.data(this, "lens-reporter", new SlideDeckLens.reporter(this))
        }), a("div.image").find("p").load(function() {
            a(".copy").removeClass("sdl-reporter-hide")
        })
    })
}(jQuery);