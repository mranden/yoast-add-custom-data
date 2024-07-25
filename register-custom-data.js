/* global YoastSEO, __yoastExtra */

class MyCustomDataPlugin {
  constructor() {
    // Ensure YoastSEO.js is present and can access the necessary features.
    if (
      typeof YoastSEO === "undefined" ||
      typeof YoastSEO.analysis === "undefined" ||
      typeof YoastSEO.analysis.worker === "undefined"
    ) {
      return;
    }

    YoastSEO.app.registerPlugin("MyCustomDataPlugin", { status: "ready" });

    this.registerModifications();
  }

  registerModifications() {
    const callback = this.addContent.bind(this);

    // Ensure that the additional data is being seen as a modification to the content.
    YoastSEO.app.registerModification(
      "content",
      callback,
      "MyCustomDataPlugin",
      10
    );
  }

  addContent(data) {
    if (__yoastExtra.page_type === "product_cat") {
      let extraData = __yoastExtra.woo_category || {};
      let catHeader = extraData.top_content || "";
      let catFooter = extraData.bottom_content || "";

      data += catHeader ? ` ${catHeader}` : "";
      data += catFooter ? ` ${catFooter}` : "";
    }

    return data;
  }
}

if (typeof YoastSEO !== "undefined" && typeof YoastSEO.app !== "undefined") {
  new MyCustomDataPlugin();
} else {
  jQuery(window).on("YoastSEO:ready", function () {
    new MyCustomDataPlugin();
  });
}
