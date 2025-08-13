<template>
  <LoadingComponent :props="{ isActive: loading }" />

  <div id="homepage" class="db-card db-tab-div active">
    <div class="db-card-header">
      <h3 class="db-card-title">Home Description</h3>
    </div>

    <div class="db-card-body">
      <form @submit.prevent="save">
        <div class="form-row">
          <!-- Title -->
          <div class="form-col-12">
            <label for="title" class="db-field-title required">
              {{ $t("label.title") }}
            </label>
            <input
              v-model="form.title"
              :class="errors.title ? 'invalid' : ''"
              type="text"
              id="title"
              class="db-field-control"
            />
            <small class="db-field-alert" v-if="errors.title">{{ errors.title[0] }}</small>
          </div>

          <!-- Description -->
          <div class="form-col-12">
            <label for="description" class="db-field-title required">
              {{ $t("label.description") }}
            </label>
            <textarea
              v-model="form.description"
              :class="errors.description ? 'invalid' : ''"
              id="description"
              class="db-field-control"
              rows="6"
            ></textarea>
            <small class="db-field-alert" v-if="errors.description">{{ errors.description[0] }}</small>
          </div>

          <!-- Save Button -->
          <div class="form-col-12">
            <button type="submit" class="db-btn text-white bg-primary">
              <i class="lab lab-fill-save"></i>
              <span>{{ $t("button.save") }}</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";

export default {
  name: "HomeComponent",
  components: { LoadingComponent },
  data() {
    return {
      loading: false,
      form: {
        title: "",
        description: "",
      },
      errors: {},
    };
  },
  mounted() {
    this.fetchHomePage();
  },
  methods: {
    async fetchHomePage() {
      this.loading = true;
      try {
        const res = await axios.get("/admin/setting/homepage");
        this.form.title = res.data.data?.title || "";
        this.form.description = res.data.data?.description || "";
      } catch (err) {
        alertService.error("Something went wrong while fetching.");
      } finally {
        this.loading = false;
      }
    },

    async save() {
      this.loading = true;
      this.errors = {};
      try {
        const res = await axios.post("/admin/setting/homepage", this.form);
        this.form.title = res.data.data?.title || "";
        this.form.description = res.data.data?.description || "";
        alertService.success("Home Description saved successfully!");
      } catch (err) {
        this.errors = err.response?.data?.errors || {};
        if (!this.errors.title && !this.errors.description) {
          alertService.error("Something went wrong while saving.");
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
