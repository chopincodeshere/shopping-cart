@extends('UI.base.main')
@section('content')
    <table id="users_table" class="display">

    </table>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let columns = [{
                        'data': "id",
                        "title": "Id"
                    },
                    {
                        'data': 'user',
                        'title': "User name",
                        'render': function(data, type, row) {
                            return data ? data.name : 'N/A';
                        }
                    },
                    {
                        'data': 'user',
                        'title': "User email",
                        'render': function(data, type, row) {
                            return data ? data.email : 'N/A';
                        }
                    },
                    {
                        'data': 'status',
                        'title': "Status",
                        'render': function(data, type, row) {
                            if (type === 'display') {
                                let statusOptions = {
                                    'PENDING': 'text-bg-warning',
                                    'COMPLETED': 'text-bg-success'
                                };

                                let dropdown = `
                                    <select class="form-select form-select-sm status-dropdown" data-id="${row.id}">
                                        ${Object.keys(statusOptions).map(status => `
                                                            <option value="${status}" ${data === status ? 'selected' : ''}>
                                                                ${status}
                                                            </option>
                                                        `).join('')}
                                    </select>
                                `;
                                return dropdown;
                            }
                            return data;
                        }
                    }

                ];

                var table = $('#users_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "pageLength": 10,
                    "order": [], //no default sorting
                    "ajax": {
                        "url": "{{ route('orders.api') }}",
                        "type": "GET",
                        "dataFilter": function(json) {
                            let jsonData = jQuery.parseJSON(json);
                            jsonData.recordsTotal = jsonData.total;
                            jsonData.recordsFiltered = jsonData.total;
                            return JSON.stringify(jsonData);
                        },
                        "data": function(data) {
                            data.page = Math.floor(data.start / data.length) + 1;
                            data.per_page = data.length;
                            if (data.order.length > 0) {
                                data.sort_field = columns[data.order[0].column].data;
                                data.sort_order = data.order[0].dir;
                            }
                            if (data.search.value != '') {
                                data.search = data.search.value;
                            } else {
                                data.search = '';
                            }


                            return data;
                        }
                    },
                    "columns": columns,
                });

                $(document).on('change', '.status-dropdown', function() {
                    let newStatus = $(this).val();
                    let rowId = $(this).data('id'); 

                    $.ajax({
                        url: '{{ route('orders.update.status') }}', 
                        method: 'POST',
                        data: {
                            order_id: rowId,
                            status: newStatus,
                            _token: $('meta[name="csrf-token"]').attr(
                                'content') 
                        },
                        success: function(response) {
                            if (response.order && response.order.status === 'COMPLETED') {
                                $(this).prop('disabled', true);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred: ' + xhr.responseText || status);
                        }
                    });
                });

            });
        </script>
    @endpush
@endsection
