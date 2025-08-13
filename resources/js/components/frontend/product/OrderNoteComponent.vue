<template>
  <!-- Delivery Details Section Start -->
  <div class="mt-12 p-4 bg-white rounded-lg shadow-sm border border-gray-200 mb-2">
    <!-- <h3 class="text-xl font-bold text-gray-800 mb-4">Delivery Details</h3> -->

    <!-- Delivery Date & Time Field -->
    <div class="mb-6">
      <label for="delivery-datetime" class="block text-lg font-semibold text-gray-800 mb-2">
        Delivery Date and Time
      </label>
      <input
        type="text"
        id="delivery-datetime"
        :value="formattedDateTime"
        @click="openDateTimeModal"
        readonly
        class="w-full p-3 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 ease-in-out"
        :placeholder="Select"
      />
    </div>

    <!-- Order Note Field -->
    <div>
      <label for="order-note" class="block text-lg font-semibold text-gray-800 mb-2">
        {{ $t('label.order_note') }}
      </label>
      <textarea
        id="order-note"
        v-model="note"
        rows="4"
        class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 ease-in-out resize-y"
        :placeholder="Instruction"
      ></textarea>
      <p class="text-sm text-gray-500 mt-2">
        Optional Note For Your Order
      </p>
    </div>
  </div>
  <!-- Delivery Details Section End -->

  <!-- Date/Time Picker Modal -->
  <div v-if="showDateTimeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full relative overflow-hidden">
      <h4 class="text-xl font-bold mb-4">Select Date and Time</h4>

      <!-- Transition for sliding effect -->
      <transition name="slide-fade" mode="out-in">
        <!-- Date Selection Step -->
        <div v-if="modalStep === 'date'" key="date-step">
          <label class="block text-sm font-medium text-gray-700 mb-1">Select Date</label>

          <div class="inline-calendar border rounded p-4 max-w-sm">
            <div class="flex justify-between items-center mb-2">
              <button @click="prevMonth" class="px-2 py-1 rounded hover:bg-gray-200">&lt;</button>
              <div class="font-semibold">{{ months[currentMonth] }} {{ currentYear }}</div>
              <button @click="nextMonth" class="px-2 py-1 rounded hover:bg-gray-200">&gt;</button>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-600 mb-1">
              <div v-for="day in weekdays" :key="day">{{ day }}</div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center">
              <div v-for="blank in blanks" :key="'b'+blank">&nbsp;</div>
              <button
                v-for="day in daysInMonth"
                :key="day"
                @click="selectDay(day)"
                :class="[
                  'p-2 rounded hover:bg-primary hover:text-white cursor-pointer',
                  isSelected(day) ? 'bg-primary text-white' : '',
                  isPastDate(day) ? 'cursor-not-allowed text-gray-400 hover:bg-transparent' : ''
                ]"
                :disabled="isPastDate(day)"
                type="button"
              >
                {{ day }}
              </button>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button @click="closeDateTimeModal"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition duration-200">
              {{ $t('button.cancel') }}
            </button>
          </div>
        </div>

        <!-- Time Selection Step -->
        <div v-else-if="modalStep === 'time'" key="time-step">
          <div class="mb-4">
            <label for="modal-time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
            <select id="modal-time" v-model="tempDeliveryTime"
                    @change="confirmDateTime"
                    class="w-full p-2 border border-gray-300 rounded-md">
              <option value="">Select Time</option>
              <option v-for="time in availableTimes" :key="time" :value="time">{{ time }}</option>
            </select>
          </div>
          <div class="flex justify-end gap-3 mt-6">
            <button @click="closeDateTimeModal"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition duration-200">
              {{ $t('button.cancel') }}
            </button>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  name: "DeliveryDetailsComponent",
  data() {
    return {
      deliveryDate: null,
      deliveryTime: null,
      showDateTimeModal: false,
      tempDeliveryDate: null,
      tempDeliveryTime: null,
      availableTimes: [
        '09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM',
        '11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM',
        '01:00 PM', '01:30 PM', '02:00 PM', '02:30 PM',
        '03:00 PM', '03:30 PM', '04:00 PM', '04:30 PM',
        '05:00 PM', '05:30 PM', '06:00 PM', '06:30 PM',
        '07:00 PM', '07:30 PM', '08:00 PM', '08:30 PM',
        '09:00 PM', '09:30 PM', '10:00 PM', '10:30 PM',
      ],
      note: '',
      modalStep: 'date',
      Select: 'Select Date and Time',
      Instruction: 'Add any special instructions here',
      currentYear: null,
      currentMonth: null,
      weekdays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      months: [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
      ],
    };
  },
  computed: {
    formattedDateTime() {
      if (this.deliveryDate && this.deliveryTime) {
        const date = new Date(this.deliveryDate);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return `${date.toLocaleDateString(undefined, options)} at ${this.deliveryTime}`;
      }
      return '';
    },
    daysInMonth() {
      return new Array(this.daysCount(this.currentYear, this.currentMonth))
        .fill(null)
        .map((_, i) => i + 1);
    },
    blanks() {
      const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
      return new Array(firstDay).fill(null);
    },
  },
  watch: {
    deliveryDate(newVal) {
      this.$emit('update:deliveryDate', newVal);
    },
    deliveryTime(newVal) {
      this.$emit('update:deliveryTime', newVal);
    },
    note(newVal) {
      this.$emit('update:note', newVal);
    }
  },
  methods: {
    daysCount(year, month) {
      return new Date(year, month + 1, 0).getDate();
    },
    selectDay(day) {
      if (this.isPastDate(day)) return;
      this.tempDeliveryDate = new Date(this.currentYear, this.currentMonth, day).toISOString().slice(0, 10);
      this.modalStep = 'time';
    },
    isSelected(day) {
      if (!this.tempDeliveryDate) return false;
      const selected = new Date(this.tempDeliveryDate);
      return selected.getDate() === day &&
             selected.getMonth() === this.currentMonth &&
             selected.getFullYear() === this.currentYear;
    },
    isPastDate(day) {
      const date = new Date(this.currentYear, this.currentMonth, day);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return date < today;
    },
    prevMonth() {
      if (this.currentMonth === 0) {
        this.currentMonth = 11;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
    },
    nextMonth() {
      if (this.currentMonth === 11) {
        this.currentMonth = 0;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
    },
    openDateTimeModal() {
      if (this.deliveryDate) {
        const d = new Date(this.deliveryDate);
        this.currentYear = d.getFullYear();
        this.currentMonth = d.getMonth();
        this.tempDeliveryDate = this.deliveryDate;
      } else {
        const now = new Date();
        this.currentYear = now.getFullYear();
        this.currentMonth = now.getMonth();
        this.tempDeliveryDate = null;
      }
      this.modalStep = 'date';
      this.showDateTimeModal = true;
    },
    closeDateTimeModal() {
      this.showDateTimeModal = false;
    },
    confirmDateTime() {
      this.deliveryDate = this.tempDeliveryDate;
      this.deliveryTime = this.tempDeliveryTime;
      this.closeDateTimeModal();
    }
  }
};
</script>

<style scoped>
.slide-fade-enter-active, .slide-fade-leave-active {
  transition: all 0.3s ease-out;
}
.slide-fade-enter-from, .slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
.inline-calendar button {
  user-select: none;
}
.bg-primary {
  background-color: #3b82f6; /* Tailwind blue-500 */
}
</style>
