@extends('layouts.app')

@php
    use Carbon\Carbon;
    use App\Constants\HttpConstant;
@endphp

@section('styles')
<style>
    .edit, .delete {
        cursor: pointer;
    }
</style>
@endsection

@section('contents')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    발급내역
                </div>
                <div class="mt-2 ml-3">
                     <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>· API키는 도매꾹 아이디 하나당 5개까지 생성할 수 있습니다.</span>
                        @if ($userRow->count() < 5)
                        <button class="btn btn-success" id="create">추가발급</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card mt-4 mb-4">
                <table class="table">
                    <thead>
                        <tr class="table-secondary">
                            <th scope="col">담당자</th>
                            <th scope="col">API 키</th>
                            <th scope="col">발급일</th>
                            <th scope="col">기능</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($userRow as $row)
                        <tr>
                            <td>{{ json_decode($row->memo)->managerName }}</td>
                            <td class="api-key" data-api-key="{{ $row->apiKey }}" value="">
                                {{ Str::limit(Str::mask($row->apiKey, '*', 10), 30, '') }}
                                <button class="btn btn-info ms-auto copy">복사</button>
                            </td>
                            <td>{{ Carbon::parse($row->dateReg)->format('Y-m-d') }}</td>
                            <td data-id="{{ $row->no }}">
                                <button class="btn btn-primary ms-auto edit">수정</button>
                                <button class="btn btn-danger ms-auto delete">삭제</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="4">API 키가 존재하지 않습니다.</td>
                        </tr>
                        @endforelse
                    </tbody>
                  </table>
            </div>
        </div>
    </div>

    <!-- 추가발급 Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">API 키 발급</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-create">
                    <div class="modal-body">
                        <label for="service-name" >서비스 명:</label>
                        <input type="text" class="form-control" id="service-name" name="serviceName" value="" required>
                        <div class="error-serviceName"></div>
                        <label for="service-url" >서비스 URL:</label>
                        <input type="text" class="form-control" id="service-url" name="serviceUrl" value="">
                        <div class="error-serviceUrl"></div>
                        <label for="manager-name" >담당자 성명:</label>
                        <input type="text" class="form-control" id="manager-name" name="managerName" value="">
                        <div class="error-managerName"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal">닫기</button>
                        <button type="button" class="btn btn-success" id="btn-create">발급</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 수정 Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">API 키 수정</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-edit">
                    <div class="modal-body">
                        <label for="edit-service-name" >서비스 명:</label>
                        <input type="text" class="form-control" id="edit-service-name" name="serviceName" value="">
                        <div class="error-serviceName"></div>
                        <label for="edit-service-url" >서비스 URL:</label>
                        <input type="text" class="form-control" id="edit-service-url" name="serviceUrl" value="">
                        <div class="error-serviceUrl"></div>
                        <label for="edit-manager-name" >담당자 성명:</label>
                        <input type="text" class="form-control" id="edit-manager-name" name="managerName" value="">
                        <div class="error-managerName"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal">닫기</button>
                        <button type="button" class="btn btn-primary" id="btn-edit">수정</button>
                        <input type="hidden" name="editNo" value="">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 삭제 Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">API 키 삭제</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    정말로 삭제하시겠습니까?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal">닫기</button>
                    <button type="button" class="btn btn-danger" id="btn-delete">삭제</button>
                    <input type="hidden" name="deleteNo" value="">
                </div>
            </div>
        </div>
    </div>


    @section('scripts')
    <script>
        $(document).ready(function () {
            clickCopyButton();
            showCreateModal();
            showEditModal();
            showDeleteModal();

            $(document).on('click', '.btn-close', function () {
                let id = $(this).closest('.modal').attr('id');
                $(".form-control").val("");
                $('.alert-danger').text('');
                $('.alert-danger').removeClass('alert alert-danger');
                $('#'+id).modal('hide');
            });

            $(document).on('click', '#btn-create', function () {
                let formData = new FormData($('#form-create')[0]);
                let dataObject = {};
                formData.forEach(function(value, key) {
                    dataObject[key] = value;
                });
                dataObject.id = @json(Session::has('id') ? Session::get('id') : "");
                dataObject.name = @json(Session::has('name') ? Session::get('name') : "");
                createAjax(dataObject);
            });

            $(document).on('click', '#btn-edit', function () {
                let formData = new FormData($('#form-edit')[0]);
                let dataObject = {};
                formData.forEach(function(value, key) {
                    dataObject[key] = value;
                })
                updateAjax(dataObject);
            });

            $(document).on('click', '#btn-delete', function () {
                let no = $("input[name=deleteNo]").val();
                deleteAjax({no});
            });

            $('.modal').on('click', function (e) {
                if ($(e.target).hasClass('modal')) {
                    $('.alert-danger').text('');
                    $('.alert-danger').removeClass('alert alert-danger');
                }
            });
        });

        /**
         * 클립보드 복사
         *
         * @return  {void}
         */
        function clickCopyButton() {
            $('.copy').click(function (e) {
                e.preventDefault();
                let key = $(this).closest('td').data('apiKey');

                navigator.clipboard.writeText(key).then(function() {
                    alert('복사완료');
                }).catch(function(err) {
                    console.error('Failed to copy API Key: ', err);
                });
            });
        }

        /**
         * 추가발급 Modal
         *
         * @return  {void}
         */
        function showCreateModal() {
            $('#create').click(function (e) {
                e.preventDefault();
                $('#createModal').modal('show');
            });
        }

        /**
         * 수정 Modal
         *
         * @return  {void}
         */
        function showEditModal() {
            $(".edit").click(function (e) {
                e.preventDefault();
                let no = $(this).closest('td').data('id');
                editAjax(no);
                $('#editModal').modal('show');
            });
        }

        /**
         * 삭제 Modal
         *
         * @return  {void}
         */
        function showDeleteModal() {
            $(".delete").click(function (e) {
                e.preventDefault();
                let id = $(this).closest('td').data('id');
                $("input[name=deleteNo]").val(id);
                $('#deleteModal').modal('show');
            });
            return false;
        }

        /**
         * 추가발급
         *
         * @param   {object}  params  {id:회원아이디, name:회원이름, serviceName:서비스명, serviceUrl:서비스URL, managerName:담당자성명}
         *
         * @return  {void}
         */
        function createAjax(params)
        {
            $.ajax({
                headers: {"X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")},
                type: "post",
                url: "{{ route('key.store') }}",
                data: params,
                dataType: "json",
                success: function (response) {
                    if (response.result == "{{ HttpConstant::RETURN_SUCCESS }}") {
                        alert("생성 완료");
                        location.reload();
                    }
                    else {
                        alert("관리자에게 문의해 주세요:1");
                    }
                },
                error: function (xhr, error, status) {
                    if (xhr.status == "{{ HttpConstant::UNPROCESSABLE }}") {
                        displayErrors(xhr.responseJSON.errors);
                    } else {
                        alert('관리자에게 문의해 주세요:2 ('+status+'): ' + xhr.responseText);
                    }
                },
            });
        }

        /**
         * 수정 Modal Row
         *
         * @param   {number}  no  PK
         *
         * @return  {void}
         */
        function editAjax(no)
        {
            $.ajax({
                headers: {"X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")},
                type: "post",
                url: "{{ route('key.edit') }}",
                data: {no},
                success: function (response) {
                    if (response.result == "{{ HttpConstant::RETURN_SUCCESS }}") {
                        let memo = JSON.parse(response.list.memo);
                        $("#edit-service-name").val(memo.serviceName);
                        $("#edit-service-url").val(memo.serviceUrl);
                        $("#edit-manager-name").val(memo.managerName);
                        $("input[name=editNo]").val(response.list.no);
                    }
                    else {
                        alert("관리자에게 문의해 주세요:3");
                    }
                },
                error: function (xhr, error, status) {
                    if (xhr.status == "{{ HttpConstant::UNPROCESSABLE }}") {
                        displayErrors(xhr.responseJSON.errors);
                    } else {
                        alert('관리자에게 문의해 주세요:4 ('+status+'): '+xhr.responseText);
                    }
                },
            });
        }

        /**
         * 수정
         *
         * @param   {object}  params  {serviceName:서비스명, serviceUrl:서비스URL, serviceName:서비스명, editNo: PK}
         *
         * @return  {void}
         */
        function updateAjax(params)
        {
            $.ajax({
                headers: {"X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")},
                type: "post",
                url: "{{ route('key.update') }}",
                data: params,
                dataType: "json",
                success: function (response) {
                    if (response.result == "{{ HttpConstant::RETURN_SUCCESS }}") {
                        alert("수정 완료");
                        location.reload();
                    }
                    else {
                        alert("관리자에게 문의해 주세요:5");
                    }
                },
                error: function (xhr, error, status) {
                    if (xhr.status == "{{ HttpConstant::UNPROCESSABLE }}") {
                        displayErrors(xhr.responseJSON.errors);
                    } else {
                        alert('관리자에게 문의해 주세요:6 ('+status+'): ' + xhr.responseText);
                    }
                },
            });
        }

        /**
         * 삭제
         *
         * @param   {object}  params  {no:PK}
         *
         * @return  {void}
         */
        function deleteAjax(params)
        {
            $.ajax({
                headers: {"X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")},
                type: "post",
                url: "{{ route('key.destroy') }}",
                data: params,
                dataType: "json",
                success: function (response) {
                    if (response.result == "{{ HttpConstant::RETURN_SUCCESS }}") {
                        alert("삭제 완료");
                        location.reload();
                    }
                    else {
                        alert("관리자에게 문의해 주세요:7");
                    }
                },
                error: function (xhr, error, status) {
                    alert('관리자에게 문의해 주세요:8 ('+status+'): ' + xhr.responseText);
                },
            });
        }

        /**
         * 유효성 결과 처리
         *
         * @param   {object}
         *
         * @return  {void}
         */
        function displayErrors(errors) {
            $('.alert-danger').text('');
            for (var field in errors) {
                if (errors.hasOwnProperty(field)) {
                    $('.error-' + field).addClass('alert alert-danger');
                    $('.error-' + field).text(errors[field].join(', '));
                }
            }
        }
    </script>
    @endsection
@endsection
