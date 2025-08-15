<template>
  <div v-if="visible" class="modal-overlay">
    <div class="modal-container">
      <button @click="$emit('close')" class="modal-close-button">&times;</button>

      <div class="modal-header">
        <h3>Select Add-ons</h3>
      </div>


      <div class="modal-body">
        <div class="add-on-grid">
          <div
            v-for="product in filteredAddons"
            :key="product.id"
            class="addon-card"
          >
            <img
              :src="getProductImage(product)"
              :alt="product.name"
            />
            <p class="product-name">{{ product.name }}</p>
              <p class="product-price">{{ product.price || product.discounted_price || product.old_price || 0 }}</p>
            <button @click="toggleAddon(product)">
              {{ isSelected(product) ? 'Remove' : 'Add' }}
            </button>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <div class="price-summary">
          <p>Base Item: {{ basePrice }}</p>
          <p>Add-ons: {{ addonsPrice }}</p>
          <p><strong>Total: {{ typeof total === 'number' ? total : Number(total) }}</strong></p>
        </div>
        <button class="continue-btn" @click="continueOrder">Continue</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProductPopupComponent",
  props: {
    visible: { type: Boolean, default: false },
    basePrice: { type: Number, required: true },
    shippingPrice: { type: Number, default: 99 },
    addons: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    baseUrl: { type: String, default: '' },
    baseProductId: { type: [Number, String], default: null }
  },
  data() {
    return {
      selectedCategory: null,
      selectedAddons: []
    };
  },
  watch: {
    // Automatically select first category
    categories: {
      immediate: true,
      handler(newCats) {
        if (newCats.length && !this.selectedCategory) {
          this.selectedCategory = newCats[0].id;
        }
      }
    }
  },
  computed: {
    filteredAddons() {
      // Exclude the main product from add-ons
      if (!Array.isArray(this.addons)) return [];
      if (!this.baseProductId) return this.addons;
      return this.addons.filter(p => p.product_id !== this.baseProductId && p.id !== this.baseProductId);
    },
    addonsPrice() {
      function parsePrice(val) {
        if (typeof val === 'string') {
          val = val.replace(/[^\d.\-]/g, '');
        }
        return Number(val) || 0;
      }
      return this.selectedAddons.reduce((sum, addon) => {
        let price = 0;
        if (addon.price !== undefined && addon.price !== null && addon.price !== '') {
          price = parsePrice(addon.price);
        } else if (addon.discounted_price !== undefined && addon.discounted_price !== null && addon.discounted_price !== '') {
          price = parsePrice(addon.discounted_price);
        } else if (addon.old_price !== undefined && addon.old_price !== null && addon.old_price !== '') {
          price = parsePrice(addon.old_price);
        }
        return sum + price;
      }, 0);
    },
    total() {
      const base = Number(this.basePrice) || 0;
      const addons = Number(this.addonsPrice) || 0;
      const shipping = Number(this.shippingPrice) || 0;
      return base + addons + shipping;
    }
  },
  methods: {
    toggleAddon(product) {
      const key = product.product_id ?? product.id;
      const index = this.selectedAddons.findIndex(p => (p.product_id ?? p.id) === key);
      let newArr;
      if (index > -1) {
        newArr = this.selectedAddons.filter(p => (p.product_id ?? p.id) !== key);
      } else {
        newArr = [...this.selectedAddons, product];
      }
      if (typeof this.$set === 'function') {
        this.$set(this, 'selectedAddons', newArr);
      } else {
        this.selectedAddons = newArr;
      }
      // Debug: log selectedAddons and their price fields
      console.log('Selected Addons:', this.selectedAddons.map(a => ({
        id: a.id,
        product_id: a.product_id,
        price: a.price,
        discounted_price: a.discounted_price,
        old_price: a.old_price
      })));
      console.log('AddonsPrice:', this.addonsPrice);
    },
    isSelected(product) {
      const key = product.product_id ?? product.id;
      return this.selectedAddons.some(p => (p.product_id ?? p.id) === key);
    },
    continueOrder() {
      console.log('Popup: continueOrder called');
      this.$emit("continue", {
        basePrice: this.basePrice,
        selectedAddons: this.selectedAddons,
        total: this.total
      });
    },
    getProductImage(product) {
      const imageUrl = product.image || product.cover;
      if (!imageUrl) return '';
      if (imageUrl.startsWith('http') || imageUrl.startsWith('//')) {
        return imageUrl;
      }
      return `${this.baseUrl || ''}/storage/products/${imageUrl}`;
    }
  },
  mounted() {
    console.log('Selected Category:', this.selectedCategory);
    console.log('Addons:', this.addons);
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed; top:0; left:0; right:0; bottom:0;
  background: rgba(0,0,0,0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 1000;
}
.modal-container {
  background: #fff;
  width: 90%;
  max-width: 900px;
  border-radius: 10px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: 90vh;
}
.modal-close-button {
  background: transparent; border: none; font-size: 1.5rem; cursor: pointer;
  position: absolute; top: 15px; right: 15px;
  z-index: 1001;
}
.modal-header { padding: 15px; border-bottom: 1px solid #eee; text-align: center; }
.category-tabs {
  display: flex;
  overflow-x: auto;
  border-bottom: 1px solid #eee;
  padding: 5px 10px;
  flex-shrink: 0;
}
.category-tabs button {
  padding: 8px 15px; margin-right: 5px; background: #f5f5f5; border: none; border-radius: 20px; cursor: pointer;
  white-space: nowrap;
}
.category-tabs button.active { background: #d1d700; color: #fff; }
.modal-body {
  flex-grow: 1;
  overflow-y: auto;
  padding: 15px;
}
.add-on-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
.addon-card {
  background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 10px; text-align: center;
  display: flex; flex-direction: column; justify-content: space-between;
}
.addon-card img { width: 100%; height: 100px; object-fit: cover; margin-bottom: 8px; border-radius: 8px; }
.product-name { font-weight: bold; margin-bottom: 4px; font-size: 0.9em; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.product-price { margin-bottom: 8px; font-size: 0.8em; }
.modal-footer {
  padding: 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;
  flex-shrink: 0;
}
.price-summary p { margin: 3px 0; }
.continue-btn { background: #a2ad00; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }

@media (max-width: 768px) {
  .modal-container { width: 95%; }
  .add-on-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
}
</style>