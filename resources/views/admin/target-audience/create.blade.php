<x-layout.default>
    <div class="panel">
        <div class="flex xl:flex-row flex-col gap-2.5">
            <div class="panel px-0 flex-1 py-6 ltr:xl:mr-6 rtl:xl:ml-6">
                @if(!empty($target_audience))
                <h5 class="font-semibold text-lg dark:text-white-light ml-2">Edit Target Audience</h5>
                @else
                <h5 class="font-semibold text-lg dark:text-white-light ml-2">Add Target Audience</h5>
                @endif
                <hr class="border-[#e0e6ed] dark:border-[#1b2e4b] my-6">
                <div class="px-5">
                    @if(!empty($target_audience))
                    <form method="POST" action="{{ route('admin.target-audience.update', $target_audience->id) }}" class="space-y-5">
                        @csrf
                        @else
                        <form method="POST" action="{{route('admin.target-audience.store')}}" class="space-y-5">
                            @csrf
                            @endif
                            <div>
                                <label for="Target Audience">Target Audience<span class="text-danger">*</spna></label>
                                <input id="targetAudience" type="text" placeholder="Enter Target Audience.." name="target_audience" value="{{!empty($target_audience->name) ? $target_audience->name:''}}" class="form-input" />
                                @error('target_audience')
                                <div class="invalid-feedback text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="flex flex-col sm:flex-row">
                                <div class="flex-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <button type="submit" class="btn btn-primary ">Submit</button>
                                    <a href="" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.default>