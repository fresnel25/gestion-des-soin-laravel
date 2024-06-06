@extends('layouts.master')

@section('title')
{{ __('Create Notification') }}
@endsection

@section('content')
<div class="row justify-content-center">
   <div class="col-md-8">
      <div class="card shadow mb-4">
         <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Add New Notification') }}</h6>
         </div>
         <div class="card-body">

            <form method="post" action="{{ route('notification.store') }}">
               <div class="form-group">
                  <label for="title">{{ __('sentence.Title') }}</label>
                  <input type="text" class="form-control" name="title" id="title" aria-describedby="title">
                  {{ csrf_field() }}
               </div>
               <div class="form-group">
                  <label for="content">{{ __('sentence.Content') }}</label>
                  <textarea class="form-control" name="content" id="content" aria-describedby="content" cols="30" rows="5"></textarea>
                  {{ csrf_field() }}
               </div>
               <div class="form-group">
                  <label for="type">{{ __('sentence.Color') }}</label>
                  <select type="text" class="form-control" name="type" id="type">
                     <option value="success">Success</option>
                     <option value="danger">Danger</option>
                     <option value="warning">Warning</option>
                     <option value="info">Info</option>
                  </select>
               </div>
               <div class="form-group">
                  <label for="start_date">{{ __('sentence.Start Date') }}</label>
                  <input type="date" class="form-control" name="start_date" id="start_date">
               </div>
               <div class="form-group">
                  <label for="end_date">{{ __('sentence.End Date') }}</label>
                  <input type="date" class="form-control" name="end_date" id="end_date">
               </div>

               <button type="submit" class="btn btn-primary">{{ __('sentence.Save') }}</button>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection
