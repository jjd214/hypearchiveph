@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Store products')
@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>All Consignments</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        All Consignments
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
           <a href="{{ route('admin.consignment.add-consign') }}" class="btn btn-primary">Add new consignment</a>
        </div>
    </div>
</div>

<div class="mb-20" >
    <ul>
        <li><small><span class="badge badge-success">Expiry date</span> : 30 Days to expired</small></li>
        <li><small><span class="badge badge-warning">Expiry date</span> : 7 Days to expired</small> </li>
        <li><small><span class="badge badge-danger">Expiry date</span> : Below 7 days to expired</small> </li>
        <li><small><span class="badge badge-secondary">Expiry date</span> : Expired</small> </li>
    </ul>
</div>

<div class="card-box mb-20 pd-20">
    @livewire('admin.consign')
</div>

@endsection
