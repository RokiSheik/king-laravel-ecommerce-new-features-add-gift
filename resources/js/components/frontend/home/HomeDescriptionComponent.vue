<template>
  <section class="pt-8 pb-24 sm:py-12 border-t border-slate-100 w-full">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="w-full px-10">
        <!-- Display title -->
        <h2 class="text-2xl font-semibold mb-4 text-center">{{ title }}</h2>

        <!-- Render HTML content (full or short) -->
        <div
          class="mx-auto"
          v-html="showMore || !isLongDescription ? description : shortDescription + '...'">
        </div>

        <div v-if="isLongDescription" class="mt-2 text-center">
          <button
            @click="toggleShowMore"
            class="text-primary underline cursor-pointer">
            {{ showMore ? 'Show Less' : 'Show More' }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";

export default {
  name: "HomeDescriptionComponent",
  data() {
    return {
      title: "",
      description: "",
      loading: true,
      error: null,
      showMore: false,
      shortDescription: "",
      isLongDescription: false,
    };
  },
  methods: {
    toggleShowMore() {
      this.showMore = !this.showMore;
    },

    getShortDescription(desc, maxBlocks = 3) {
      const div = document.createElement("div");
      div.innerHTML = desc;

      const blocks = Array.from(div.children);
      const shortBlocks = blocks.slice(0, maxBlocks);

      return shortBlocks.map(b => b.outerHTML).join("");
    },

    styleRichText(html) {
      // Headings
      html = html.replace(/<h1>/gi, '<h1 class="text-4xl font-bold mb-4">');
      html = html.replace(/<h2>/gi, '<h2 class="text-3xl font-semibold mb-3">');
      html = html.replace(/<h3>/gi, '<h3 class="text-2xl font-semibold mb-2">');
      html = html.replace(/<h4>/gi, '<h4 class="text-xl font-semibold mb-2">');
      html = html.replace(/<h5>/gi, '<h5 class="text-lg font-medium mb-1">');
      html = html.replace(/<h6>/gi, '<h6 class="text-base font-medium mb-1">');

      // Paragraphs
      html = html.replace(/<p>/gi, '<p class="text-base mb-2">');

      // Strong / bold
      html = html.replace(/<strong>/gi, '<strong class="font-bold">');

      // Italic / emphasis
      html = html.replace(/<em>/gi, '<em class="italic">');

      // Underline
      html = html.replace(/<u>/gi, '<u class="underline">');

      // Blockquote
      html = html.replace(/<blockquote>/gi, '<blockquote class="border-l-4 border-gray-300 pl-4 italic mb-4">');

      // Lists
      html = html.replace(/<ul>/gi, '<ul class="list-disc pl-5 mb-2">');
      html = html.replace(/<ol>/gi, '<ol class="list-decimal pl-5 mb-2">');
      html = html.replace(/<li>/gi, '<li class="mb-1">');

      // Links
      html = html.replace(/<a /gi, '<a class="text-blue-600 hover:underline" ');

      // Images
      html = html.replace(/<img /gi, '<img class="my-2 max-w-full h-auto" ');

      return html;
    },
  },

  mounted() {
    axios.get("/frontend/homepage")
      .then((response) => {
        const data = response.data.data || response.data;
        this.title = data.title || "";
        let rawDescription = data.description || "";

        // Style all tags
        const styledDescription = this.styleRichText(rawDescription);

        // Count words
        const div = document.createElement("div");
        div.innerHTML = rawDescription;
        const textOnly = div.textContent || div.innerText || "";
        const wordCount = textOnly.split(" ").length;
        this.isLongDescription = wordCount > 100;

        // Set description and shortDescription
        this.description = styledDescription;
        this.shortDescription = this.isLongDescription
          ? this.getShortDescription(styledDescription, 3)
          : styledDescription;

        this.loading = false;
      })
      .catch(() => {
        this.error = "Failed to load homepage info.";
        this.loading = false;
      });
  },
};
</script>
