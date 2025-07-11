<x-layout.default>

    <div x-data="contacts">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-xl">Campaigns</h2>
            <div class="flex sm:flex-row flex-col sm:items-center sm:gap-3 gap-4 w-full sm:w-auto">
                <div class="flex gap-3">
                    <div>
                        <button type="button" class="btn btn-primary" @click="editUser()">

                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
                                <circle cx="10" cy="6" r="4" stroke="currentColor"
                                    stroke-width="1.5" />
                                <path opacity="0.5"
                                    d="M18 17.5C18 19.9853 18 22 10 22C2 22 2 19.9853 2 17.5C2 15.0147 5.58172 13 10 13C14.4183 13 18 15.0147 18 17.5Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path d="M21 10H19M19 10H17M19 10L19 8M19 10L19 12" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            Add Campaign
                        </button>
                        <div class="fixed inset-0 bg-[black]/60 z-[999] overflow-y-auto hidden"
                            :class="addContactModal && '!block'">
                            <div class="flex items-center justify-center min-h-screen px-4"
                                @click.self="addContactModal = false">
                                <div x-show="addContactModal" x-transition x-transition.duration.300
                                    class="panel border-0 p-0 rounded-lg overflow-hidden md:w-full max-w-lg w-[90%] my-8">
                                    <button type="button"
                                        class="absolute top-4 ltr:right-4 rtl:left-4 text-white-dark hover:text-dark"
                                        @click="addContactModal = false">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                    <h3 class="text-lg font-medium bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]"
                                        x-text="params.id ? 'Edit Campaigns' : 'Add Campaigns'"></h3>
                                    <div class="p-5">
                                        <form @submit.prevent="saveUser">
                                            @csrf 
                                            <div class="mb-5">
                                                <label for="name">Name</label>
                                                <input id="name" type="text" placeholder="Enter Name"
                                                    class="form-input" x-model="params.name" />
                                            </div>
                                            <div class="mb-5">
                                                <label for="description">Description</label>
                                                <textarea id="description" placeholder="Enter Description"
                                                    class="form-input" x-model="params.description"></textarea>
                                            </div>
                                            <div class="mb-5">
                                                <label for="budget">budget</label>
                                                <input id="budget" type="number" placeholder="Enter budget"
                                                    class="form-input" x-model="params.budget" />
                                            </div>

                                            <div class="mb-5">
                                                <label for="type">Type</label>
                                                <select id="type" class="form-input" x-model="params.type">
                                                    <option value="">Select Type</option>
                                                    <option value="email">Email</option>
                                                    <option value="sms">SMS</option>
                                                    <option value="push">Push Notification</option>
                                                    <option value="social">Social Media</option>
                                                </select>
                                            </div>

                                            <div class="mb-5">
                                                <label for="start_date">Start Date</label>
                                                <input id="start_date" type="date" placeholder="Enter Start Date"
                                                    class="form-input" x-model="params.start_date" />
                                            </div>

                                            <div class="mb-5">
                                                <label for="end_date">End Date</label>
                                                <input id="end_date" type="date" placeholder="Enter End Date"
                                                    class="form-input" x-model="params.end_date" />
                                            </div>

                                            <div class="mb-5">
                                                <label>Users</label>
                                                <select id="user" class="form-input" x-model="params.user_id">
                                                    <option value="">Select User</option>
                                                    <template x-for="user in allUsers" :key="user.id">
                                                        <option :value="user.id" x-text="user.name"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            <div class="mb-5">
                                                <label>Status</label>
                                                <select id="status" class="form-input" x-model="params.status">
                                                    <option value="">Select Status</option>
                                                    <option value="draft">Draft</option>
                                                    <option value="active">Active</option>
                                                    <option value="paused">Paused</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="archived">Archived</option>
                                                </select>
                                            </div>

                                            <div class="mb-5">
                                                <label>Target Audience</label>
                                                <select id="target_audience" class="form-input" x-model="params.target_audience_id">
                                                    <option value="">Select Target Audience</option>
                                                    <template x-for="audience in allTargetAudiences" :key="audience.id">
                                                        <option :value="audience.id" x-text="audience.name"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            <div class="mb-5">
                                                <label>Goals</label>
                                                <select id="goals" class="form-input" x-model="params.goal_id">
                                                    <option value="">Select Goal</option>
                                                    <template x-for="goal in allGoals" :key="goal.id">
                                                        <option :value="goal.id" x-text="goal.name"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            <div class="mb-5">
                                                <label for="ctnFile">file input</label>
                                                <input id="ctnFile" type="file" class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 rtl:file:ml-5 file:text-white file:hover:bg-primary" x-model="params.avatar" />
                                            </div>
                                  
                                            
                                            <div class="flex justify-end items-center mt-8">
                                                <button type="button" class="btn btn-outline-danger"
                                                    @click="addContactModal = false">Cancel</button>
                                                <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4"
                                                    x-text="params.id ? 'Update' : 'Add'"></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary p-2"
                            :class="{ 'bg-primary text-white': displayType === 'list' }"
                            @click="setDisplayType('list')">

                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path d="M2 5.5L3.21429 7L7.5 3" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path opacity="0.5" d="M2 12.5L3.21429 14L7.5 10" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M2 19.5L3.21429 21L7.5 17" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M22 19L12 19" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                                <path opacity="0.5" d="M22 12L12 12" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                                <path d="M22 5L12 5" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary p-2"
                            :class="{ 'bg-primary text-white': displayType === 'grid' }"
                            @click="setDisplayType('grid')">

                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path opacity="0.5"
                                    d="M2.5 6.5C2.5 4.61438 2.5 3.67157 3.08579 3.08579C3.67157 2.5 4.61438 2.5 6.5 2.5C8.38562 2.5 9.32843 2.5 9.91421 3.08579C10.5 3.67157 10.5 4.61438 10.5 6.5C10.5 8.38562 10.5 9.32843 9.91421 9.91421C9.32843 10.5 8.38562 10.5 6.5 10.5C4.61438 10.5 3.67157 10.5 3.08579 9.91421C2.5 9.32843 2.5 8.38562 2.5 6.5Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path opacity="0.5"
                                    d="M13.5 17.5C13.5 15.6144 13.5 14.6716 14.0858 14.0858C14.6716 13.5 15.6144 13.5 17.5 13.5C19.3856 13.5 20.3284 13.5 20.9142 14.0858C21.5 14.6716 21.5 15.6144 21.5 17.5C21.5 19.3856 21.5 20.3284 20.9142 20.9142C20.3284 21.5 19.3856 21.5 17.5 21.5C15.6144 21.5 14.6716 21.5 14.0858 20.9142C13.5 20.3284 13.5 19.3856 13.5 17.5Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path
                                    d="M2.5 17.5C2.5 15.6144 2.5 14.6716 3.08579 14.0858C3.67157 13.5 4.61438 13.5 6.5 13.5C8.38562 13.5 9.32843 13.5 9.91421 14.0858C10.5 14.6716 10.5 15.6144 10.5 17.5C10.5 19.3856 10.5 20.3284 9.91421 20.9142C9.32843 21.5 8.38562 21.5 6.5 21.5C4.61438 21.5 3.67157 21.5 3.08579 20.9142C2.5 20.3284 2.5 19.3856 2.5 17.5Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path
                                    d="M13.5 6.5C13.5 4.61438 13.5 3.67157 14.0858 3.08579C14.6716 2.5 15.6144 2.5 17.5 2.5C19.3856 2.5 20.3284 2.5 20.9142 3.08579C21.5 3.67157 21.5 4.61438 21.5 6.5C21.5 8.38562 21.5 9.32843 20.9142 9.91421C20.3284 10.5 19.3856 10.5 17.5 10.5C15.6144 10.5 14.6716 10.5 14.0858 9.91421C13.5 9.32843 13.5 8.38562 13.5 6.5Z"
                                    stroke="currentColor" stroke-width="1.5" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="relative ">
                    <input type="text" placeholder="Search Campaigns"
                        class="form-input py-2 ltr:pr-11 rtl:pl-11 peer" x-model="searchUser"
                        @keyup="searchContacts" />
                    <div
                        class="absolute ltr:right-[11px] rtl:left-[11px] top-1/2 -translate-y-1/2 peer-focus:text-primary">

                        <svg class="mx-auto" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor"
                                stroke-width="1.5" opacity="0.5"></circle>
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 panel p-0 border-0 overflow-hidden">
            <template x-if="displayType === 'list'">
                <div class="table-responsive">
                    <table class="table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>User</th>
                                <th>Target Audiences</th>
                                <th>Goals</th>
                                <th class="!text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="contact in filterdContactsList" :key="contact.id">
                                <tr>
                                    <td>
                                        <div class="flex items-center w-max">
                                            <div x-show="contact.media[0]?.file_path" class="w-max">
                                                <img :src="`/storage/${contact.media[0]?.file_path}`"
                                                    class="h-8 w-8 rounded-full object-cover ltr:mr-2 rtl:ml-2"
                                                    alt="avatar" />
                                            </div>
                                            <div x-show="!contact.media[0]?.file_path && contact.name"
                                                class="grid place-content-center h-8 w-8 ltr:mr-2 rtl:ml-2 rounded-full bg-primary text-white text-sm font-semibold"
                                                x-text="contact.name.charAt(0) + '' + contact.name.charAt(contact.name.indexOf(' ') + 1)">
                                            </div>
                                            <div x-show="!contact.media[0]?.file_path && !contact.name"
                                                class="border border-gray-300 dark:border-gray-800 rounded-full p-2 ltr:mr-2 rtl:ml-2">

                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4.5 h-4.5">
                                                    <circle cx="12" cy="6" r="4"
                                                        stroke="currentColor" stroke-width="1.5"></circle>
                                                    <ellipse opacity="0.5" cx="12" cy="17"
                                                        rx="7" ry="4" stroke="currentColor"
                                                        stroke-width="1.5"></ellipse>
                                                </svg>
                                            </div>
                                            <div x-text="contact.name"></div>
                                        </div>
                                    </td>
                                    <td x-text="contact.type"></td>
                                    <td x-text="contact.budget" class="whitespace-nowrap"></td>
                                    <td x-text="contact.status" class="whitespace-nowrap"></td>
                                    <td x-text="contact.start_date" class="whitespace-nowrap"></td>
                                    <td x-text="contact.end_date" class="whitespace-nowrap"></td>
                                    <td x-text="contact.user.name" class="whitespace-nowrap"></td>
                                    <td x-text="contact.target_audiences[0]?.name" class="whitespace-nowrap"></td>
                                    <td x-text="contact.goals[0]?.name" class="whitespace-nowrap"></td>
                                    <td>
                                        <div class="flex gap-4 items-center justify-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                @click="editUser(contact)">Edit</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                @click="deleteUser(contact)">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div class="flex justify-end mt-6 items-center space-x-4" x-show="pagination.last_page > 1">
                        <!-- Total Records -->
                        <div class="text-sm text-gray-500" x-text="`Total records: ${pagination.total}`"></div>

                        <!-- Previous Button -->
                        <button
                            class="px-4 py-2 rounded-lg border text-sm font-medium transition-all duration-200 hover:bg-gray-100 disabled:opacity-50"
                            :disabled="!pagination.prev_page_url"
                            @click="fetchContacts(pagination.current_page - 1)">
                            Previous
                        </button>

                        <!-- Page Numbers -->
                        <template x-for="page in pagination.last_page" :key="page">
                            <button
                                class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
                                :class="{
                                    'bg-blue-600 text-white shadow-md': page === pagination.current_page,
                                    'border border-gray-300 text-gray-700 hover:bg-gray-100': page !== pagination.current_page
                                }"
                                @click="fetchContacts(page)"
                                x-text="page">
                            </button>
                        </template>

                        <!-- Next Button -->
                        <button
                            class="px-4 py-2 rounded-lg border text-sm font-medium transition-all duration-200 hover:bg-gray-100 disabled:opacity-50"
                            :disabled="!pagination.next_page_url"
                            @click="fetchContacts(pagination.current_page + 1)">
                            Next
                        </button>
                    </div>

                </div>
            </template>
        </div>
        <template x-if="displayType === 'grid'">
            <div class="grid 2xl:grid-cols-4 xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6 w-full">
                <template x-for="contact in filterdContactsList" :key="contact.id">
                    <div class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative">
                        <div
                            class="bg-white/40 rounded-t-md bg-[url('/assets/images/notification-bg.png')] bg-center bg-cover p-6 pb-0">
                            <template x-if="contact.avatar">
                                <img class="object-contain w-4/5 max-h-40 mx-auto"
                                    :src="`/storage/${contact.avatar}`" />
                            </template>
                        </div>
                        <div class="px-6 pb-24 -mt-10 relative">
                            <div class="shadow-md bg-white dark:bg-gray-900 rounded-md px-2 py-4">
                                <div class="text-xl" x-text="contact.name"></div>
                                <div class="text-white-dark" x-text="contact.role"></div>
                                <div class="flex items-center justify-between flex-wrap mt-6 gap-3">
                                    <div class="flex-auto">
                                        <div class="text-info" x-text="contact.posts"></div>
                                        <div>Posts</div>
                                    </div>
                                    <div class="flex-auto">
                                        <div class="text-info" x-text="contact.following"></div>
                                        <div>Following</div>
                                    </div>
                                    <div class="flex-auto">
                                        <div class="text-info" x-text="contact.followers"></div>
                                        <div>Followers</div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <ul class="flex space-x-4 rtl:space-x-reverse items-center justify-center">
                                        <li>
                                            <a href="javascript:;"
                                                class="btn btn-outline-primary p-0 h-7 w-7 rounded-full">

                                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                    class="w-4 h-4">
                                                    <path
                                                        d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;"
                                                class="btn btn-outline-primary p-0 h-7 w-7 rounded-full">

                                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                    class="w-4 h-4">
                                                    <rect x="2" y="2" width="20"
                                                        height="20" rx="5" ry="5"></rect>
                                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                                    <line x1="17.5" y1="6.5" x2="17.51"
                                                        y2="6.5"></line>
                                                </svg>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;"
                                                class="btn btn-outline-primary p-0 h-7 w-7 rounded-full">

                                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                    class="w-4 h-4">
                                                    <path
                                                        d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                                    </path>
                                                    <rect x="2" y="9" width="4"
                                                        height="12"></rect>
                                                    <circle cx="4" cy="4" r="2"></circle>
                                                </svg>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;"
                                                class="btn btn-outline-primary p-0 h-7 w-7 rounded-full">

                                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                    class="w-4 h-4">
                                                    <path
                                                        d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-6 grid grid-cols-1 gap-4 ltr:text-left rtl:text-right">
                                <div class="flex items-center">
                                    <div class="flex-none ltr:mr-2 rtl:ml-2">Description :</div>
                                    <div class="truncate text-white-dark" x-text="contact.description"></div>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-none ltr:mr-2 rtl:ml-2">Phone :</div>
                                    <div class="text-white-dark" x-text="contact.phone"></div>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-none ltr:mr-2 rtl:ml-2">Address :</div>
                                    <div class="text-white-dark" x-text="contact.location"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-4 absolute bottom-0 w-full ltr:left-0 rtl:right-0 p-6">
                            <button type="button" class="btn btn-outline-primary w-1/2"
                                @click="editUser(contact)">Edit</button>
                            <button type="button" class="btn btn-outline-danger w-1/2"
                                @click="deleteUser(contact)">Delete</button>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            Alpine.data("contacts", () => ({
    displayType: 'list',
    addContactModal: false,
    searchUser: '',
    contactList: [],
    filterdContactsList: [],
    loading: false,
    allUsers: [], // Assuming you have a list of users to display
    file_path: null, // For storing the file path of the first media
    allTargetAudiences: [], // Assuming you have a list of target audiences to display
    allGoals: [],
    defaultParams: {
        id: null,
        name: '',
        description: '',
        budget: '',
        type: '',
        start_date: '',
        end_date: '',
        status: 'draft',
        user_id: '',
        target_audience_id: '',
        goal_id: ''
    },

    params: {
        id: null,
        name: '',
        description: '',
        budget: '',
        type: '',
        start_date: '',
        end_date: '',
        status: 'draft',
        user_id: '',
        target_audience_id: '',
        goal_id: ''
    },
    pagination: {
        current_page: 1,
        last_page: 1,
        next_page_url: null,
        prev_page_url: null,
        links: [],
        total: 0,
    },

    init() {
        this.fetchContacts();
         this.fetchUsers();
         this.fetchTargetAudiences();
         this.fetchGoals();
    },

    async fetchContacts(page = 1) {
        this.loading = true;
        try {
            const response = await fetch(`/admin/get-campaigns?page=${page}`); // your Laravel API route
            const data = await response.json();
            this.contactList = data.data || data;// assuming JSON returns { users: [...] }
            console.log("data from contacts 1111111", this.contactList);
            this.file_path = data.data[0].media ? data.data[0].media[0]?.file_path : null;
            console.log("data from contacts", this.file_path);
            this.pagination = {
                current_page: data.current_page,
                last_page: data.last_page,
                next_page_url: data.next_page_url,
                prev_page_url: data.prev_page_url,
                links: data.links,
                total: data.total,
            };
            console.log("checking pagination now", this.pagination);
            this.searchContacts();
        } catch (error) {
            this.showMessage("Failed to load users", "error");
            console.error(error);
        } finally {
            this.loading = false;
        }
    },
    async fetchUsers() {
        try {
            const response = await fetch('/admin/get-users'); // Adjust endpoint as needed
            const data = await response.json();
            console.log("data from users", data.users.data);
            this.allUsers = data.users.data || [];
        } catch (e) {
            this.showMessage("Failed to load goals", "error");
        }
    },
    async fetchTargetAudiences() {
        try {
            const response = await fetch('/admin/get-target-audiences'); // Adjust endpoint as needed
            const data = await response.json();
            console.log("data from target audiences", data);
            this.allTargetAudiences = data || [];
        } catch (e) {
            this.showMessage("Failed to load target audiences", "error");
        } 
    },
    async fetchGoals() {
        try {
            const response = await fetch('/admin/get-goals'); // Adjust endpoint as needed
            const data = await response.json();
            console.log("data from goals", data);
            this.allGoals = data || [];
        } catch (e) {
            this.showMessage("Failed to load goals", "error");
        }
    },

    searchContacts() {
        const search = this.searchUser.toLowerCase();
        this.filterdContactsList = this.contactList.filter((d) =>
            d.name.toLowerCase().includes(search)
        );
    },

    editUser(user) {
        this.params = { ...this.defaultParams };
        if (user) {
            this.params = JSON.parse(JSON.stringify(user));
            if (user.start_date) {
                this.params.start_date = user.start_date.split(' ')[0];
            }
            if (user.end_date) {
                this.params.end_date = user.end_date.split(' ')[0];
            }
            if (user.target_audiences && user.target_audiences.length > 0) {
                this.params.target_audience_id = user.target_audiences[0].id;
            } else {
                this.params.target_audience_id = '';
            }
            if (user.goals && user.goals.length > 0) {
                this.params.goal_id = user.goals[0].id;
            } else {
                this.params.goal_id = '';
            }
        }
        this.addContactModal = true;
    },

    async saveUser() {
        const headers = {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        };

        console.log("checking params", !this.params.budget, !this.params.deadline, !this.params.name, !this.params.content_prompt);

         if (!this.params.name || !this.params.start_date || !this.params.end_date || this.params.budget === null || this.params.budget === '') {
            this.showMessage("All fields are required.", "error");
            return;
        }

        const isUpdate = !!this.params.id;
        const url = isUpdate ? `/admin/campaigns/${this.params.id}` : `/admin/campaigns`;
        const method = isUpdate ? "POST" : "POST"; // Still POST; Laravel will handle method spoofing for updates

        const formData = new FormData();
        formData.append('name', this.params.name);
        formData.append('budget', this.params.budget);
        formData.append('goal_id', this.params.goal_id);
        formData.append('description', this.params.description);
        formData.append('start_date', this.params.start_date);
        formData.append('end_date', this.params.end_date);
        formData.append('user_id', this.params.user_id);
        formData.append('target_audience_id', this.params.target_audience_id);
        formData.append('goal_id', this.params.goal_id);
        formData.append('type', this.params.type);
        formData.append('status', this.params.status);
        // If updating, you may include _method=PUT
        if (isUpdate) {
            console.log("have put");
            formData.append('_method', 'PUT');
        }
        
        console.log(isUpdate, "no put")

        // If a file is selected
        const fileInput = document.getElementById('ctnFile');
        if (fileInput && fileInput.files.length > 0) {
            console.log("setting media file ======", fileInput.files[0]);
            formData.append('media_file', fileInput.files[0]);
        }

        try {
            const response = await fetch(url, {
                method,
                body: formData,
                headers
            });

            const result = await response.json();

            if (response.ok) {
                this.showMessage("User has been saved successfully.");
                this.fetchContacts(); // refresh list
                this.addContactModal = false;
            } else {
                throw new Error(result.message || "Failed to save user.");
            }
        } catch (err) {
            console.error(err);
            this.showMessage(err.message, "error");
        }
    },

    async deleteUser(user) {
         const headers = {
            'X-CSRF-TOKEN': token,
        };
        try {
            const response = await fetch(`/admin/campaigns/${user.id}`, {
                method: "DELETE",
                headers: {
                    "Accept": "application/json",
                    ...headers
                }
            });

            if (!response.ok) throw new Error("Delete failed");

            this.showMessage("Campaign deleted successfully.");
            this.fetchContacts(); // refresh list
        } catch (err) {
            console.error(err);
            this.showMessage("Error deleting campaign", "error");
        }
    },

    setDisplayType(type) {
        this.displayType = type;
    },

    showMessage(msg = '', type = 'success') {
        const toast = window.Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
        });
        toast.fire({
            icon: type,
            title: msg,
            padding: '10px 20px',
        });
    },
}));

        });
    </script>
</x-layout.default>
