!(function (e) {
    var t = {};
    function c(r) {
        if (t[r]) return t[r].exports;
        var n = (t[r] = { i: r, l: !1, exports: {} });
        return e[r].call(n.exports, n, n.exports, c), (n.l = !0), n.exports;
    }
    (c.m = e),
        (c.c = t),
        (c.d = function (e, t, r) {
            c.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: r });
        }),
        (c.r = function (e) {
            "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }), Object.defineProperty(e, "__esModule", { value: !0 });
        }),
        (c.t = function (e, t) {
            if ((1 & t && (e = c(e)), 8 & t)) return e;
            if (4 & t && "object" == typeof e && e && e.__esModule) return e;
            var r = Object.create(null);
            if ((c.r(r), Object.defineProperty(r, "default", { enumerable: !0, value: e }), 2 & t && "string" != typeof e))
                for (var n in e)
                    c.d(
                        r,
                        n,
                        function (t) {
                            return e[t];
                        }.bind(null, n)
                    );
            return r;
        }),
        (c.n = function (e) {
            var t =
                e && e.__esModule
                    ? function () {
                          return e.default;
                      }
                    : function () {
                          return e;
                      };
            return c.d(t, "a", t), t;
        }),
        (c.o = function (e, t) {
            return Object.prototype.hasOwnProperty.call(e, t);
        }),
        (c.p = ""),
        c((c.s = 6));
})([
    function (e, t) {
        e.exports = window.wp.element;
    },
    function (e, t) {
        e.exports = window.wp.i18n;
    },
    function (e, t) {
        e.exports = window.wp.blocks;
    },
    function (e, t) {
        e.exports = window.wp.apiFetch;
    },
    function (e, t) {
        e.exports = window.wp.compose;
    },
    function (e, t) {
        e.exports = window.wp.components;
    },
    function (e, t, c) {
        "use strict";
        c.r(t);
        var r = c(0),
            n = c(1),
            o = c(2),
            l = c(3),
            i = c.n(l),
            s = c(4),
            f = c(5);
        const m = new Map();
        
        jQuery.post(ajaxurl, {"action":"calculation_forms_get_lists"}, function(e) { 
            Object.entries(e).forEach(([e, t]) => {
                m.set(t.id, t);
            });
            
        });
        var u = {
            from: [
                {
                    type: "shortcode",
                    tag: "calculation",
                    attributes: {
                        id: {
                            type: "integer",
                            shortcode: function (e) {
                                var t = e.named.id;
                                return parseInt(t);
                            },
                        },
                        title: {
                            type: "string",
                            shortcode: function (e) {
                                return e.named.title;
                            },
                        },
                    },
                },
            ],
            to: [
                {
                    type: "block",
                    blocks: ["core/shortcode"],
                    transform: function (e) {
                        return Object(o.createBlock)("core/shortcode", { text: '[calculation id="'.concat(e.id, '" title="').concat(e.title, '"]') });
                    },
                },
            ],
        };
        Object(o.registerBlockType)("calculation-forms/selector", {
            title: Object(n.__)("Calculation Forms", "calculation-forms"),
            description: Object(n.__)("Insert a contact form you have created with Calculation Forms", "calculation-forms"),
            category: "widgets",
            attributes: { id: { type: "integer" }, title: { type: "string" } },
            icon: "feedback",
            transforms: u,
            edit: function e({ attributes: t, setAttributes: c }) {
                if (!m.size && !t.id)
                    return Object(r.createElement)("div", { className: "components-placeholder" }, Object(r.createElement)("p", null, Object(n.__)("No contact forms were found. Create a contact form first.", "calculation-forms")));
                const o = Array.from(m.values(), (e) => ({ value: e.id, label: e.title }));
                if (t.id) o.length || o.push({ value: t.id, label: t.title });
                else {
                    const e = o[0];
                    t = { id: parseInt(e.value), title: e.label };
                }
                const a = "calculation-forms-" + Object(s.useInstanceId)(e);
                return Object(r.createElement)(
                    "div",
                    { className: "components-placeholder" },
                    Object(r.createElement)("label", { htmlFor: a, className: "components-placeholder__instructions" }, Object(n.__)("Display a calculation form:", "calculation-forms")),
                    Object(r.createElement)(f.SelectControl, { id: a, options: o, value: t.id, onChange: (e) => c({ id: parseInt(e), title: m.get(parseInt(e)).title }) })
                );
            },
            save: function (e) {
                var t = e.attributes;
                return Object(r.createElement)("div", null, '[calculation id="', t.id, '" title="', t.title, '"]');
            },
        });
    },
]);
