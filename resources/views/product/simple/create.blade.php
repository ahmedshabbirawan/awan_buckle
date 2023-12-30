@extends('Layout.master')

@section('title')
 Create Product
@endsection

@section('content')

<div class="page-content">
<div class="page-header"><h1>Products</h1></div>

@include('product.simple.form')

</div>

@endsection

