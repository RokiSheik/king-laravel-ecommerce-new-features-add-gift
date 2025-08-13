<template>
    <!-- Delivery Details Section Start -->
    <div class="mt-12 p-4 bg-white rounded-lg shadow-sm border border-gray-200 mb-2">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Delivery Details</h3>

        <!-- Country and Address Fields (on same line) -->
        <div class="flex flex-col md:flex-row md:gap-4 mb-6">
            <!-- Country Field -->
            <div class="mb-6 md:mb-0 flex-1">
                <label for="delivery-country" class="block text-lg font-semibold text-gray-800 mb-2">
                    Country
                </label>
                <select
                    id="delivery-country"
                    v-model="selectedCountryCode"
                    @change="handleCountryChange"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 ease-in-out"
                >
                    <option value="">Select Country</option>
                    <option v-for="c in countries" :key="c.code" :value="c.code">{{ c.name }}</option>
                </select>
            </div>

            <!-- Address Field -->
            <div class="flex-1">
                <label for="delivery-address" class="block text-lg font-semibold text-gray-800 mb-2">
                    Delivery Address
                </label>
                <input
                    type="text"
                    id="delivery-address"
                    v-model="address"
                    @input="debounceAddressSearch"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 ease-in-out"
                    :placeholder="Address"
                />
                <ul v-if="filteredAddressSuggestions.length > 0 && address.length > 0"
                    class="mt-2 border border-gray-300 rounded-md bg-white max-h-48 overflow-y-auto z-10 relative">
                    <li v-for="(suggestion, index) in filteredAddressSuggestions" :key="index"
                        @click="selectAddress(suggestion.text)"
                        class="p-3 cursor-pointer hover:bg-gray-100 border-b border-gray-200 last:border-b-0">
                        {{ suggestion.text }}
                    </li>
                </ul>
            </div>
        </div>

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
                    <div class="mb-4">
                        <label for="modal-date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" id="modal-date" v-model="tempDeliveryDate"
                               @change="handleDateSelection"
                               class="w-full p-2 border border-gray-300 rounded-md"
                               ref="dateInput">
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
            loading: {
                isActive: false,
            },
            selectedCountryCode: 'BD', // Default to Bangladesh's ISO code
            countries: [], // Will be populated dynamically from API
            address: '',
            filteredAddressSuggestions: [],
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
            addressSearchTimeout: null,
            googleMapsApiKey: 'YOUR_GOOGLE_MAPS_API_KEY', // Replace with your actual key
            // Re-added data properties for placeholders
            Address: 'Enter Delivery Address',
            Select: 'Select Date and Time',
            Instruction: 'Add any special instructions here',
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
        }
    },
    watch: {
        selectedCountryCode(newVal) {
            this.$emit('update:country', newVal);
            this.address = '';
            this.filteredAddressSuggestions = [];
            this.fetchAddressSuggestions(''); // Re-trigger search for the new country
        },
        address(newVal) {
            this.$emit('update:address', newVal);
        },
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
    mounted() {
        this.fetchCountries();
        this.fetchAddressSuggestions('');
    },
    methods: {
        async fetchCountries() {
            this.loading.isActive = true;
            try {
                const response = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.countries = data.map(country => ({
                    name: country.name.common,
                    code: country.cca2
                })).sort((a, b) => a.name.localeCompare(b.name));

                if (!this.selectedCountryCode || !this.countries.some(c => c.code === this.selectedCountryCode)) {
                    this.selectedCountryCode = 'BD';
                }
            } catch (error) {
                console.error("Error fetching countries:", error);
                this.countries = [
                    { name: 'Bangladesh', code: 'BD' },
                    { name: 'United States', code: 'US' },
                    { name: 'United Kingdom', code: 'GB' }
                ];
                this.selectedCountryCode = 'BD';
            } finally {
                this.loading.isActive = false;
            }
        },

        handleCountryChange() {
            this.address = '';
            this.filteredAddressSuggestions = [];
            this.fetchAddressSuggestions(''); // Trigger a new search for the new country
        },

        debounceAddressSearch() {
            clearTimeout(this.addressSearchTimeout);
            this.addressSearchTimeout = setTimeout(() => {
                this.fetchAddressSuggestions(this.address);
            }, 300);
        },

        async fetchAddressSuggestions(query) {
            this.loading.isActive = true;
            this.filteredAddressSuggestions = [];

            if (!query && !this.selectedCountryCode) {
                this.loading.isActive = false;
                return;
            }

            try {
                // This is the new list of 50 Bangladeshi addresses
                const allBangladeshiAddresses = [
                    { text: 'House 1, Road 1, Block A, Bashundhara R/A, Dhaka 1229', country: 'BD' },
                    { text: 'Apartment 5B, Green Road, Dhanmondi, Dhaka 1205', country: 'BD' },
                    { text: 'Shop 12, New Market, Mirpur Road, Dhaka 1205', country: 'BD' },
                    { text: 'Flat 3C, Gulshan Avenue, Gulshan 1, Dhaka 1212', country: 'BD' },
                    { text: 'Sector 7, Road 11, House 22, Uttara, Dhaka 1230', country: 'BD' },
                    { text: 'Plot 45, Kawran Bazar, Tejgaon, Dhaka 1215', country: 'BD' },
                    { text: 'Agrabad Commercial Area, Chittagong 4100', country: 'BD' },
                    { text: 'Bandarban Sadar, Bandarban 4600', country: 'BD' },
                    { text: 'Cox\'s Bazar Sea Beach Road, Cox\'s Bazar 4700', country: 'BD' },
                    { text: 'Sylhet Sadar, Zindabazar, Sylhet 3100', country: 'BD' },
                    { text: 'Shahjalal University of Science and Technology, Sylhet 3114', country: 'BD' },
                    { text: 'Rajshahi University Campus, Rajshahi 6205', country: 'BD' },
                    { text: 'Boalia Thana, Rajshahi 6000', country: 'BD' },
                    { text: 'Khulna City Corporation, Khulna 9000', country: 'BD' },
                    { text: 'Jessore Sadar, Jessore 7400', country: 'BD' },
                    { text: 'Mymensingh Medical College, Mymensingh 2200', country: 'BD' },
                    { text: 'Kishoreganj Sadar, Kishoreganj 2300', country: 'BD' },
                    { text: 'Comilla Town Hall, Comilla 3500', country: 'BD' },
                    { text: 'Feni Sadar, Feni 3900', country: 'BD' },
                    { text: 'Noakhali Sadar, Noakhali 3800', country: 'BD' },
                    { text: 'Barisal Sadar, Barisal 8200', country: 'BD' },
                    { text: 'Patuakhali Science and Technology University, Patuakhali 8602', country: 'BD' },
                    { text: 'Rangpur Medical College, Rangpur 5400', country: 'BD' },
                    { text: 'Dinajpur Sadar, Dinajpur 5200', country: 'BD' },
                    { text: 'Bogra Sadar, Bogra 5800', country: 'BD' },
                    { text: 'Pabna Sadar, Pabna 6600', country: 'BD' },
                    { text: 'Narayanganj Sadar, Narayanganj 1400', country: 'BD' },
                    { text: 'Gazipur Chowrasta, Gazipur 1700', country: 'BD' },
                    { text: 'Tongi, Gazipur 1711', country: 'BD' },
                    { text: 'Savar, Dhaka 1340', country: 'BD' },
                    { text: 'Dhamrai, Dhaka 1350', country: 'BD' },
                    { text: 'Keraniganj, Dhaka 1310', country: 'BD' },
                    { text: 'Fatullah, Narayanganj 1420', country: 'BD' },
                    { text: 'Siddhirganj, Narayanganj 1430', country: 'BD' },
                    { text: 'Ashulia, Savar, Dhaka 1341', country: 'BD' },
                    { text: 'Badda, Dhaka 1212', country: 'BD' },
                    { text: 'Rampura, Dhaka 1219', country: 'BD' },
                    { text: 'Malibagh, Dhaka 1217', country: 'BD' },
                    { text: 'Moghbazar, Dhaka 1217', country: 'BD' },
                    { text: 'Farmgate, Dhaka 1215', country: 'BD' },
                    { text: 'Mohammadpur, Dhaka 1207', country: 'BD' },
                    { text: 'Lalmatia, Dhaka 1207', country: 'BD' },
                    { text: 'Old Dhaka, Dhaka 1100', country: 'BD' },
                    { text: 'Banglabazar, Dhaka 1100', country: 'BD' },
                    { text: 'Kamrangirchar, Dhaka 1211', country: 'BD' },
                    { text: 'Demra, Dhaka 1361', country: 'BD' },
                    { text: 'Jatrabari, Dhaka 1204', country: 'BD' },
                    { text: 'Postogola, Dhaka 1204', country: 'BD' },
                    { text: 'Shyampur, Dhaka 1204', country: 'BD' },
                    { text: 'Hazaribagh, Dhaka 1209', country: 'BD' }
                ];

                let dynamicSuggestions = allBangladeshiAddresses;

                // Filter by query if provided
                if (query) {
                    dynamicSuggestions = dynamicSuggestions.filter(s =>
                        s.text.toLowerCase().includes(query.toLowerCase())
                    );
                }
                // Only show suggestions if the selected country is Bangladesh (BD)
                // This ensures the mock list is only used when BD is selected
                if (this.selectedCountryCode === 'BD') {
                    this.filteredAddressSuggestions = dynamicSuggestions.slice(0, 3);
                } else {
                    // If a non-BD country is selected, clear suggestions (as mock data is only for BD)
                    this.filteredAddressSuggestions = [];
                    // In a real API integration, you'd make a call here for the selected country
                }

            } catch (error) {
                console.error("Error fetching address suggestions:", error);
                this.filteredAddressSuggestions = [];
            } finally {
                this.loading.isActive = false;
            }
        },

        selectAddress(suggestionText) {
            this.address = suggestionText;
            this.filteredAddressSuggestions = [];
        },
        openDateTimeModal() {
            this.tempDeliveryDate = this.deliveryDate;
            this.modalStep = 'date';
            this.showDateTimeModal = true;
            this.$nextTick(() => {
                if (this.$refs.dateInput) {
                    this.$refs.dateInput.focus();
                    try {
                        this.$refs.dateInput.showPicker();
                    } catch (e) {
                        console.warn("showPicker() not supported by browser for date input.");
                    }
                }
            });
        },
        handleDateSelection() {
            this.modalStep = 'time';
            if (this.availableTimes.length > 0) {
                if (!this.tempDeliveryTime || !this.availableTimes.includes(this.tempDeliveryTime)) {
                    this.tempDeliveryTime = this.availableTimes[0];
                }
            } else {
                this.tempDeliveryTime = '';
            }
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
</style>
