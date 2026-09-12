(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var createElement = wp.element.createElement;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var PanelBody = wp.components.PanelBody;
  var SelectControl = wp.components.SelectControl;
  var TextControl = wp.components.TextControl;
  var __ = wp.i18n.__;

  registerBlockType("medboard/jobs-widget", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var blockProps = useBlockProps({
        className: "medboard-jobs-widget-editor",
      });

      return createElement(
        "div",
        blockProps,
        createElement(
          InspectorControls,
          null,
          createElement(
            PanelBody,
            { title: __("Medboard Jobs", "medboard-jobs-widget"), initialOpen: true },
            createElement(SelectControl, {
              label: __("Theme override", "medboard-jobs-widget"),
              value: attributes.theme || "",
              options: [
                { label: __("Use plugin settings", "medboard-jobs-widget"), value: "" },
                { label: "card", value: "card" },
                { label: "list", value: "list" },
                { label: "compact", value: "compact" },
              ],
              onChange: function (value) {
                setAttributes({ theme: value });
              },
            }),
            createElement(SelectControl, {
              label: __("Locale override", "medboard-jobs-widget"),
              value: attributes.locale || "",
              options: [
                { label: __("Use plugin settings", "medboard-jobs-widget"), value: "" },
                { label: "bg", value: "bg" },
                { label: "en", value: "en" },
              ],
              onChange: function (value) {
                setAttributes({ locale: value });
              },
            }),
            createElement(SelectControl, {
              label: __("Embed mode override", "medboard-jobs-widget"),
              value: attributes.mode || "",
              options: [
                { label: __("Use plugin settings", "medboard-jobs-widget"), value: "" },
                { label: "iframe", value: "iframe" },
                { label: "script", value: "script" },
              ],
              onChange: function (value) {
                setAttributes({ mode: value });
              },
            }),
            createElement(TextControl, {
              label: __("Page size (0 = settings)", "medboard-jobs-widget"),
              type: "number",
              value: attributes.page_size || 0,
              onChange: function (value) {
                setAttributes({ page_size: parseInt(value, 10) || 0 });
              },
            }),
            createElement(TextControl, {
              label: __("iframe height (0 = settings)", "medboard-jobs-widget"),
              type: "number",
              value: attributes.height || 0,
              onChange: function (value) {
                setAttributes({ height: parseInt(value, 10) || 0 });
              },
            })
          )
        ),
        createElement(
          "div",
          {
            style: {
              padding: "16px",
              border: "1px dashed #4DAFCB",
              borderRadius: "8px",
              background: "#f7fbfd",
            },
          },
          createElement("strong", null, __("Medboard Jobs", "medboard-jobs-widget")),
          createElement(
            "p",
            { style: { margin: "8px 0 0" } },
            __(
              "Jobs render on the front end. Configure the token under Settings → Medboard Jobs.",
              "medboard-jobs-widget"
            )
          )
        )
      );
    },
    save: function () {
      // Dynamic block — PHP render_callback outputs markup.
      return null;
    },
  });
})(window.wp);
