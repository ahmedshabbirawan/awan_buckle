@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')

@include('project.form')


<!--end row-->

@endsection

@section('javascript')
<script>
    function deleteConfirmation(objID) {
        $('#delete_form').attr('action', '{{ $adminURL }}/' + objID);
        $('#deletePopup').modal('show');
    }

    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection