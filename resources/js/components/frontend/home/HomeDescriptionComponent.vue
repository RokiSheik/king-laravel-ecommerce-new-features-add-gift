<template>
    <LoadingComponent :props="loading" />
    <section class="pt-8 pb-24 sm:py-12 border-t border-slate-100 w-full">
        <div class="px-4 sm:px-6 lg:px-8"> <!-- Optional padding -->
            <div class="w-full px-10">
                <p class="text-sm text-center">This is the Testing Description for Home Page. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.

</p>
            </div>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent.vue";

export default {
    name: "HomeDescriptionComponent",
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            }
        };
    }
};
</script>
