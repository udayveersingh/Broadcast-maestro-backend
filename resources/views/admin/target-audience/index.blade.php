<x-layout.default>


    <div x-data="TargetAudienceList">
        <script src="/assets/js/simple-datatables.js"></script>

        @if (session('success'))
        <div class="flex items-center p-3.5 rounded text-success bg-success-light dark:bg-success-dark-light">
            <span class="ltr:pr-2 rtl:pl-2">
                <strong class="ltr:mr-1 rtl:ml-1">Success!</strong> {{ session('success') }}
            </span>
            <button type="button" class="ltr:ml-auto rtl:mr-auto hover:opacity-80" onclick="this.parentElement.remove()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" class="w-5 h-5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        @endif

        <div class="panel px-0 border-[#e0e6ed] dark:border-[#1b2e4b]">
            <div class="px-5">
                <div class="md:absolute md:top-5 ltr:md:left-5 rtl:md:right-5">
                    <div class="flex items-center gap-2 mb-5">
                        <button type="button" class="btn btn-danger gap-2" @click="deleteRow()">

                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path d="M20.5001 6H3.5" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round"></path>
                                <path
                                    d="M18.8334 8.5L18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80368 18.054 5.62669 15.3991L5.16675 8.5"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                <path opacity="0.5" d="M9.5 11L10 16" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round"></path>
                                <path opacity="0.5" d="M14.5 11L14 16" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round"></path>
                                <path opacity="0.5"
                                    d="M6.5 6C6.55588 6 6.58382 6 6.60915 5.99936C7.43259 5.97849 8.15902 5.45491 8.43922 4.68032C8.44784 4.65649 8.45667 4.62999 8.47434 4.57697L8.57143 4.28571C8.65431 4.03708 8.69575 3.91276 8.75071 3.8072C8.97001 3.38607 9.37574 3.09364 9.84461 3.01877C9.96213 3 10.0932 3 10.3553 3H13.6447C13.9068 3 14.0379 3 14.1554 3.01877C14.6243 3.09364 15.03 3.38607 15.2493 3.8072C15.3043 3.91276 15.3457 4.03708 15.4286 4.28571L15.5257 4.57697C15.5433 4.62992 15.5522 4.65651 15.5608 4.68032C15.841 5.45491 16.5674 5.97849 17.3909 5.99936C17.4162 6 17.4441 6 17.5 6"
                                    stroke="currentColor" stroke-width="1.5"></path>
                            </svg>
                            Delete </button>
                        <a href="{{route('admin.target-audience.create')}}" class="btn btn-primary gap-2">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="w-5 h-5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add New </a>
                    </div>
                </div>
            </div>

            <table id="targetAudienceTable" class="whitespace-nowrap"></table>

        </div>
    </div>

    <script>
        window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        document.addEventListener("alpine:init", () => {
            Alpine.data('TargetAudienceList', () => ({
                selectedRows: [],
                items: [],
                searchText: '',
                datatable: null,
                dataArr: [],

                init() {
                    // ✅ Fetch dynamic data from API
                    fetch('/admin/target-audience/data')
                        .then(res => res.json())
                        .then(data => {
                            // Fix any name/title inconsistency
                            this.items = data.map(item => ({
                                id: item.id,
                                name: item.name,
                                action: item.action
                            }));
                            this.setTableData();
                            this.initializeTable();
                        }).catch(error => console.error('Error loading risk categories:', error));

                    this.$watch('items', () => {
                        this.datatable?.destroy();
                        this.setTableData();
                        this.initializeTable();
                    });

                    this.$watch('selectedRows', () => {
                        this.datatable?.destroy();
                        this.setTableData();
                        this.initializeTable();
                    });
                },

                initializeTable() {
                    this.datatable = new simpleDatatables.DataTable('#targetAudienceTable', {
                        data: {
                            headings: [
                                '<input type="checkbox" class="form-checkbox" :checked="checkAllCheckbox" :value="checkAllCheckbox" @change="checkAll($event.target.checked)"/>',
                                "Name",
                                "Actions",
                            ],
                            data: this.dataArr
                        },
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                                select: 0,
                                sortable: false,
                                render: (data) => {
                                    return `<input type="checkbox" class="form-checkbox mt-1" :id="'chk' + ${data}" :value="(${data})" x-model.number='selectedRows' />`;
                                }
                            },
                            {
                                select: 2,
                                sortable: false,
                                render: (data, cell, row) => {
                                    const id = row[0]; // get the id from first column of the row
                                    console.log(cell, 'test row');
                                    return `<div class="flex gap-4 items-center">
                                                <a href="/admin/target-audience/${data}/edit" class="hover:text-info">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5">
                                                        <path
                                                            opacity="0.5"
                                                            d="M22 10.5V12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2H13.5"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                            stroke-linecap="round"
                                                        ></path>
                                                        <path
                                                            d="M17.3009 2.80624L16.652 3.45506L10.6872 9.41993C10.2832 9.82394 10.0812 10.0259 9.90743 10.2487C9.70249 10.5114 9.52679 10.7957 9.38344 11.0965C9.26191 11.3515 9.17157 11.6225 8.99089 12.1646L8.41242 13.9L8.03811 15.0229C7.9492 15.2897 8.01862 15.5837 8.21744 15.7826C8.41626 15.9814 8.71035 16.0508 8.97709 15.9619L10.1 15.5876L11.8354 15.0091C12.3775 14.8284 12.6485 14.7381 12.9035 14.6166C13.2043 14.4732 13.4886 14.2975 13.7513 14.0926C13.9741 13.9188 14.1761 13.7168 14.5801 13.3128L20.5449 7.34795L21.1938 6.69914C22.2687 5.62415 22.2687 3.88124 21.1938 2.80624C20.1188 1.73125 18.3759 1.73125 17.3009 2.80624Z"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                        ></path>
                                                        <path
                                                            opacity="0.5"
                                                            d="M16.6522 3.45508C16.6522 3.45508 16.7333 4.83381 17.9499 6.05034C19.1664 7.26687 20.5451 7.34797 20.5451 7.34797M10.1002 15.5876L8.4126 13.9"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                        ></path>
                                                    </svg>
                                                </a>
                                                <button type="button" class="hover:text-danger" @click="deleteRow(${data})">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                                        <path d="M20.5001 6H3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                                        <path
                                                            d="M18.8334 8.5L18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80368 18.054 5.62669 15.3991L5.16675 8.5"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                            stroke-linecap="round"
                                                        ></path>
                                                        <path opacity="0.5" d="M9.5 11L10 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                                        <path opacity="0.5" d="M14.5 11L14 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                                        <path
                                                            opacity="0.5"
                                                            d="M6.5 6C6.55588 6 6.58382 6 6.60915 5.99936C7.43259 5.97849 8.15902 5.45491 8.43922 4.68032C8.44784 4.65649 8.45667 4.62999 8.47434 4.57697L8.57143 4.28571C8.65431 4.03708 8.69575 3.91276 8.75071 3.8072C8.97001 3.38607 9.37574 3.09364 9.84461 3.01877C9.96213 3 10.0932 3 10.3553 3H13.6447C13.9068 3 14.0379 3 14.1554 3.01877C14.6243 3.09364 15.03 3.38607 15.2493 3.8072C15.3043 3.91276 15.3457 4.03708 15.4286 4.28571L15.5257 4.57697C15.5433 4.62992 15.5522 4.65651 15.5608 4.68032C15.841 5.45491 16.5674 5.97849 17.3909 5.99936C17.4162 6 17.4441 6 17.5 6"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                        ></path>
                                                    </svg>
                                                </button>
                                            </div>`;
                                }
                            }
                        ],
                        layout: {
                            top: "{search}",
                            bottom: "{info}{select}{pager}",
                        },
                        labels: {
                            perPage: "<span class='ml-2'>{select}</span>",
                            noRows: "No data available",
                        }
                    });
                },

                checkAllCheckbox() {
                    return this.items.length && this.selectedRows.length === this.items.length;
                },

                checkAll(isChecked) {
                    if (isChecked) {
                        this.selectedRows = this.items.map(item => item.id);
                    } else {
                        this.selectedRows = [];
                    }
                },

                setTableData() {
                    this.dataArr = this.items.map((item, index) => [
                        item.id, // Column 0 (hidden or used only for reference)
                        item.name, // Column 1 (Title)
                        item.id // Column 2 (used in action buttons)
                    ]);
                },

                deleteRow(itemId) {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        padding: '2em',
                        customClass: 'sweet-alerts',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // 🔥 Proceed with delete
                            fetch(`/admin/target-audiences/${itemId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': window.csrfToken,
                                        'Accept': 'application/json',
                                    }
                                })
                                .then(res => {
                                    if (!res.ok) throw new Error('Delete failed');
                                    return res.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        this.items = this.items.filter(item => item.id !== itemId);
                                        this.datatable?.destroy();
                                        this.setTableData();
                                        this.initializeTable();

                                        // ✅ Show success alert
                                        window.Swal.fire({
                                            title: 'Deleted!',
                                            text: 'The item has been deleted.',
                                            icon: 'success',
                                            customClass: 'sweet-alerts',
                                        });
                                    }
                                })
                                .catch(err => {
                                    window.Swal.fire({
                                        title: 'Error!',
                                        text: 'Something went wrong while deleting.',
                                        icon: 'error',
                                        customClass: 'sweet-alerts',
                                    });
                                    console.error(err);
                                });
                        }
                    });
                }

            }));
        });
    </script>
</x-layout.default>