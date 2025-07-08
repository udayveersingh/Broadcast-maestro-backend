<x-layout.default>
    <div x-data="campaignEdit">
        <div class="flex xl:flex-row flex-col gap-2.5">
            <div class="panel px-0 flex-1 py-6 ltr:xl:mr-6 rtl:xl:ml-6">
                <div class="flex justify-between flex-wrap px-4">
                    <div class="mb-6 lg:w-1/2 w-full">
                        <div class="flex items-center text-black dark:text-white shrink-0">
                            <img src="/assets/images/logo.svg" alt="image" class="w-14" />
                        </div>
                        <div class="space-y-1 mt-6 text-gray-500 dark:text-gray-400">
                            <div>Marketing Department</div>
                            <div>campaigns@company.com</div>
                            <div>+1 (070) 123-4567</div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 w-full lg:max-w-fit">
                        <div class="flex items-center">
                            <label for="campaignId" class="flex-1 ltr:mr-2 rtl:ml-2 mb-0">Campaign ID</label>
                            <input id="campaignId" type="text" name="campaign-id" class="form-input lg:w-[250px] w-2/3"
                                placeholder="#001" x-model="params.id" readonly />
                        </div>
                        <div class="flex items-center mt-4">
                            <label for="campaignName" class="flex-1 ltr:mr-2 rtl:ml-2 mb-0">Campaign Name</label>
                            <input id="campaignName" type="text" name="campaign-name"
                                class="form-input lg:w-[250px] w-2/3" placeholder="Enter Campaign Name"
                                x-model="params.name" />
                        </div>
                        <div class="flex items-center mt-4">
                            <label for="startDate" class="flex-1 ltr:mr-2 rtl:ml-2 mb-0">Start Date</label>
                            <input id="startDate" type="datetime-local" name="start-date" class="form-input lg:w-[250px] w-2/3"
                                x-model="params.start_date" />
                        </div>
                        <div class="flex items-center mt-4">
                            <label for="endDate" class="flex-1 ltr:mr-2 rtl:ml-2 mb-0">End Date</label>
                            <input id="endDate" type="datetime-local" name="end-date" class="form-input lg:w-[250px] w-2/3"
                                x-model="params.end_date" />
                        </div>
                    </div>
                </div>
                <hr class="border-[#e0e6ed] dark:border-[#1b2e4b] my-6">
                <div class="mt-8 px-4">
                    <div class="flex justify-between lg:flex-row flex-col">
                        <div class="lg:w-1/2 w-full ltr:lg:mr-6 rtl:lg:ml-6 mb-6">
                            <div class="text-lg font-semibold">Campaign Details</div>
                            <div class="mt-4 flex items-center">
                                <label for="campaign-type" class="ltr:mr-2 rtl:ml-2 w-1/3 mb-0">Type</label>
                                <select id="campaign-type" name="campaign-type" class="form-input flex-1"
                                    x-model="params.type">
                                    <option value="email">Email Campaign</option>
                                    <option value="social">Social Media</option>
                                    <option value="print">Print Media</option>
                                    <option value="digital">Digital Ads</option>
                                </select>
                            </div>
                            <div class="mt-4 flex items-center">
                                <label for="campaign-status" class="ltr:mr-2 rtl:ml-2 w-1/3 mb-0">Status</label>
                                <select id="campaign-status" name="campaign-status" class="form-input flex-1"
                                    x-model="params.status">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="paused">Paused</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="mt-4 flex items-center">
                                <label for="campaign-budget" class="ltr:mr-2 rtl:ml-2 w-1/3 mb-0">Budget</label>
                                <input id="campaign-budget" type="number" name="campaign-budget"
                                    class="form-input flex-1" x-model="params.budget" placeholder="Enter Budget" 
                                    step="0.01" min="0" />
                            </div>
                            <div class="mt-4">
                                <label for="campaign-description" class="ltr:mr-2 rtl:ml-2 w-1/3 mb-2">Description</label>
                                <textarea id="campaign-description" name="campaign-description"
                                    class="form-textarea" x-model="params.description" 
                                    placeholder="Enter Campaign Description" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="lg:w-1/2 w-full">
                            <div class="text-lg font-semibold">Campaign Owner</div>
                            <div class="flex items-center mt-4">
                                <label for="owner-name" class="ltr:mr-2 rtl:ml-2 w-1/3 mb-0">Name</label>
                                <input id="owner-name" type="text" name="owner-name" class="form-input flex-1"
                                    x-model="params.user.name" placeholder="Enter Owner Name" />
                            </div>
                            <div class="flex items-center mt-4">
                                <label for="owner-email" class="ltr:mr-2 rtl:ml-2 w-1/3 mb-0">Email</label>
                                <input id="owner-email" type="email" name="owner-email"
                                    class="form-input flex-1" x-model="params.user.email" placeholder="Enter Owner Email" />
                            </div>
                            <div class="flex items-center mt-4">
                                <label for="owner-role" class="ltr:mr-2 rtl:ml-2 w-1/3 mb-0">Role</label>
                                <input id="owner-role" type="text" name="owner-role"
                                    class="form-input flex-1" x-model="params.user.role" placeholder="Enter Role" />
                            </div>
                            <div class="flex items-center mt-4">
                                <label for="owner-status" class="ltr:mr-2 rtl:ml-2 w-1/3 mb-0">User Status</label>
                                <select id="owner-status" name="owner-status" class="form-input flex-1"
                                    x-model="params.user.status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Media Section -->
                <div class="mt-8 px-4">
                    <div class="text-lg font-semibold mb-4">Campaign Media</div>
                    <template x-if="params.media && params.media.length > 0">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="(media, index) in params.media" :key="media.id">
                                <div class="border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-sm" x-text="media.original_name"></h4>
                                            <p class="text-xs text-gray-500 mt-1" x-text="media.mime_type"></p>
                                        </div>
                                        <button type="button" @click="removeMedia(media)" 
                                            class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <template x-if="media.mime_type.startsWith('image/')">
                                        <img :src="media.file_path" :alt="media.alt_text || media.original_name" 
                                            class="w-full h-32 object-cover rounded">
                                    </template>
                                    <template x-if="!media.mime_type.startsWith('image/')">
                                        <div class="w-full h-32 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <p>Size: <span x-text="formatFileSize(media.file_size)"></span></p>
                                        <p>Uploaded: <span x-text="formatDate(media.created_at)"></span></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!params.media || params.media.length === 0">
                        <div class="text-center py-8 text-gray-500">
                            No media files attached to this campaign
                        </div>
                    </template>
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" @click="addMedia()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Media
                        </button>
                    </div>
                </div>

                <!-- Campaign Timeline -->
                <div class="mt-8 px-4">
                    <div class="text-lg font-semibold mb-4">Campaign Timeline</div>
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Created</label>
                                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="formatDate(params.created_at)"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Last Updated</label>
                                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="formatDate(params.updated_at)"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Sidebar -->
            <div class="xl:w-96 w-full xl:mt-0 mt-6">
                <div class="panel mb-5">
                    <div class="mb-4">
                        <label for="priority">Campaign Priority</label>
                        <select id="priority" name="priority" class="form-select" x-model="campaignPriority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="tags">Tags</label>
                        <input id="tags" type="text" name="tags" class="form-input"
                            placeholder="Enter tags separated by commas" x-model="campaignTags" />
                    </div>
                    <div class="mb-4">
                        <label for="target-audience">Target Audience</label>
                        <textarea id="target-audience" name="target-audience" class="form-textarea"
                            placeholder="Describe target audience" x-model="targetAudience" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="panel">
                    <div class="grid xl:grid-cols-1 lg:grid-cols-4 sm:grid-cols-2 grid-cols-1 gap-4">
                        <button type="button" class="btn btn-success w-full gap-2" @click="saveCampaign()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2 shrink-0">
                                <path
                                    d="M3.46447 20.5355C4.92893 22 7.28595 22 12 22C16.714 22 19.0711 22 20.5355 20.5355C22 19.0711 22 16.714 22 12C22 11.6585 22 11.4878 21.9848 11.3142C21.9142 10.5049 21.586 9.71257 21.0637 9.09034C20.9516 8.95687 20.828 8.83317 20.5806 8.58578L15.4142 3.41944C15.1668 3.17206 15.0431 3.04835 14.9097 2.93631C14.2874 2.414 13.4951 2.08581 12.6858 2.01515C12.5122 2 12.3415 2 12 2C7.28595 2 4.92893 2 3.46447 3.46447C2 4.92893 2 7.28595 2 12C2 16.714 2 19.0711 3.46447 20.5355Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path
                                    d="M17 22V21C17 19.1144 17 18.1716 16.4142 17.5858C15.8284 17 14.8856 17 13 17H11C9.11438 17 8.17157 17 7.58579 17.5858C7 18.1716 7 19.1144 7 21V22"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path opacity="0.5" d="M7 8H13" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                            Save Campaign
                        </button>

                        <button type="button" class="btn btn-info w-full gap-2" @click="launchCampaign()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2 shrink-0">
                                <path
                                    d="M17.4975 18.4851L20.6281 9.09373C21.8764 5.34874 22.5006 3.47624 21.5122 2.48782C20.5237 1.49939 18.6511 2.12356 14.906 3.37189L5.57477 6.48218C3.49295 7.1761 2.45203 7.52305 2.13608 8.28637C2.06182 8.46577 2.01692 8.65596 2.00311 8.84963C1.94433 9.67365 2.72018 10.4495 4.27188 12.0011L4.55451 12.2837C4.80921 12.5384 4.93655 12.6658 5.03282 12.8075C5.22269 13.0871 5.33046 13.4143 5.34393 13.7519C5.35076 13.9232 5.32403 14.1013 5.27057 14.4574C5.07488 15.7612 4.97703 16.4131 5.0923 16.9147C5.32205 17.9146 6.09599 18.6995 7.09257 18.9433C7.59255 19.0656 8.24576 18.977 9.5522 18.7997L9.62363 18.79C9.99191 18.74 10.1761 18.715 10.3529 18.7257C10.6738 18.745 10.9838 18.8496 11.251 19.0285C11.3981 19.1271 11.5295 19.2585 11.7923 19.5213L12.0436 19.7725C13.5539 21.2828 14.309 22.0379 15.1101 21.9985C15.3309 21.9877 15.5479 21.9365 15.7503 21.8474C16.4844 21.5244 16.8221 20.5113 17.4975 18.4851Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path opacity="0.5" d="M6 18L21 3" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                            Launch Campaign
                        </button>

                        <a href="/campaigns/preview" class="btn btn-primary w-full gap-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2 shrink-0">
                                <path opacity="0.5"
                                    d="M3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C4.97196 6.49956 7.81811 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957Z"
                                    stroke="currentColor" stroke-width="1.5"></path>
                                <path
                                    d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
                                    stroke="currentColor" stroke-width="1.5"></path>
                            </svg>
                            Preview
                        </a>

                        <button type="button" class="btn btn-secondary w-full gap-2" @click="exportCampaign()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2 shrink-0">
                                <path opacity="0.5"
                                    d="M17 9.00195C19.175 9.01406 20.3529 9.11051 21.1213 9.8789C22 10.7576 22 12.1718 22 15.0002V16.0002C22 18.8286 22 20.2429 21.1213 21.1215C20.2426 22.0002 18.8284 22.0002 16 22.0002H8C5.17157 22.0002 3.75736 22.0002 2.87868 21.1215C2 20.2429 2 18.8286 2 16.0002L2 15.0002C2 12.1718 2 10.7576 2.87868 9.87889C3.64706 9.11051 4.82497 9.01406 7 9.00195"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                <path d="M12 2L12 15M12 15L9 11.5M12 15L15 11.5" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data('campaignEdit', () => ({
                params: {
                    id: 1,
                    name: '',
                    description: '',
                    type: 'email',
                    status: 'draft',
                    start_date: '',
                    end_date: '',
                    budget: 0,
                    user: {
                        id: 1,
                        name: '',
                        email: '',
                        role: 'user',
                        status: 'active'
                    },
                    media: [],
                    created_at: '',
                    updated_at: ''
                },
                campaignPriority: 'medium',
                campaignTags: '',
                targetAudience: '',

                init() {
                    // Load campaign data from your backend
                    this.loadCampaignData();
                },

                loadCampaignData() {
                    // This would typically be an API call to load your campaign data
                    // For demo purposes, using the structure you provided
                    const sampleData = {
                        "id": 1,
                        "user_id": 1,
                        "name": "Sustainability Drive",
                        "description": "Campaign for eco-awareness",
                        "type": "email",
                        "status": "draft",
                        "start_date": "2025-07-01T10:00:00",
                        "end_date": "2025-07-05T18:00:00",
                        "budget": "1200.00",
                        "created_at": "2025-06-27T15:50:27.000000Z",
                        "updated_at": "2025-06-27T15:50:27.000000Z",
                        "user": {
                            "id": 1,
                            "name": "John Doe",
                            "email": "john@example.com",
                            "email_verified_at": null,
                            "avatar": "avatars/oCm3b1SDpUjxDjiTXYlMFCsmufZIax2O7EiwPwAl.png",
                            "role": "user",
                            "status": "active",
                            "created_at": "2025-06-27T15:47:53.000000Z",
                            "updated_at": "2025-07-03T07:52:27.000000Z"
                        },
                        "media": [{
                            "id": 1,
                            "user_id": 1,
                            "filename": "j5C9PGQjKgzZ0FbgOwDKBYpfw3wpndVOiay0axDN.png",
                            "original_name": "image.png",
                            "file_path": "/uploads/media/j5C9PGQjKgzZ0FbgOwDKBYpfw3wpndVOiay0axDN.png",
                            "file_size": 374795,
                            "mime_type": "image/png",
                            "alt_text": null,
                            "created_at": "2025-06-27T15:50:28.000000Z",
                            "updated_at": "2025-06-27T15:50:28.000000Z",
                            "pivot": {
                                "campaign_id": 1,
                                "media_library_id": 1
                            }
                        }]
                    };

                    // Map the data to your params
                    this.params = { ...sampleData };
                    
                    // Format dates for datetime-local inputs
                    this.params.start_date = this.formatDateForInput(sampleData.start_date);
                    this.params.end_date = this.formatDateForInput(sampleData.end_date);
                },

                formatDateForInput(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toISOString().slice(0, 16); // Format for datetime-local input
                },

                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                addMedia() {
                    // Handle file upload logic here
                    console.log('Add media functionality');
                },

                removeMedia(media) {
                    this.params.media = this.params.media.filter(m => m.id !== media.id);
                },

                saveCampaign() {
                    // Handle save campaign logic
                    console.log('Saving campaign:', this.params);
                    // Make API call to save campaign
                },

                launchCampaign() {
                    // Handle launch campaign logic
                    console.log('Launching campaign:', this.params);
                },

                exportCampaign() {
                    // Handle export campaign logic
                    console.log('Exporting campaign:', this.params);
                }
            }));
        });
    </script>
</x-layout.default>