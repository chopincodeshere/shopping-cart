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
                },{
                        "data": "email",
                        "title": "Email"
                    },
                    {
                        "data": "name",
                        "title": "Name",
                    },
                    {
                        "data": "role",
                        "title": "Role"
                    },
                ];

                var table = $('#users_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "pageLength": 10,
                    "order": [], //no default sorting
                    "ajax": {
                        "url": "{{ route('users.api') }}",
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
            });
        </script>
    @endpush
@endsection