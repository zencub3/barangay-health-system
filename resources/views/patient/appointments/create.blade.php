<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schedule Consultation Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ symptom: '{{ old('symptoms', '') }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                
                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('appointments.create') }}">
                    @csrf

                    <!-- Preferred Consultation Date -->
                    <div class="mb-4">
                        <label for="consultation_date" class="block font-medium text-sm text-gray-700">Preferred Consultation Date</label>
                        <input type="date" id="consultation_date" name="consultation_date" value="{{ old('consultation_date') }}" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- Available Time Slot -->
                    <div class="mb-4">
                        <label for="time_slot" class="block font-medium text-sm text-gray-700">Available Time Slot</label>
                        <select id="time_slot" name="time_slot" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="morning" {{ old('time_slot') == 'morning' ? 'selected' : '' }}>Morning (8:00 AM - 12:00 PM)</option>
                            <option value="afternoon" {{ old('time_slot') == 'afternoon' ? 'selected' : '' }}>Afternoon (1:00 PM - 4:00 PM)</option>
                        </select>
                    </div>

                    <!-- Reason for Consultation / Common Symptoms Dropdown -->
                    <div class="mb-4">
                        <label for="symptoms" class="block font-medium text-sm text-gray-700">Reason for Consultation / Common Symptoms</label>
                        <select id="symptoms" name="symptoms" x-model="symptom" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled selected>-- Select primary reason for visit --</option>
                            <option value="Prenatal Checkup">Prenatal Checkup / Maternal Care</option>
                            <option value="Immunization / Vaccination">Immunization / Vaccination (Child or Adult)</option>
                            <option value="Fever, Cough, or Colds">Fever, Cough, or Colds (Respiratory)</option>
                            <option value="High Blood Pressure Monitoring">High Blood Pressure Check (Hypertension Management)</option>
                            <option value="Diabetes Follow-up / Blood Sugar">Diabetes Management / Blood Sugar Check</option>
                            <option value="Wound Dressing or Minor Injury">Wound Dressing, Cuts, or Minor Injury</option>
                            <option value="General Medical Consultation">General Medical Consultation</option>
                            <option value="Toothache / Dental Consultation">Toothache / Dental Consultation</option>
                            <option value="Others">Others (General Checkup)</option>
                        </select>
                    </div>

                    <!-- Conditional Text Box (Shows ONLY when "Others" is selected) -->
                    <div class="mb-4" x-show="symptom === 'Others'" x-transition style="display: none;">
                        <label for="other_service" class="block font-medium text-sm text-gray-700">
                            Please specify other service/s needed <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="other_service" name="other_service" value="{{ old('other_service') }}" 
                               :required="symptom === 'Others'"
                               placeholder="Enter specific service description..."
                               class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Submit Appointment Request
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>