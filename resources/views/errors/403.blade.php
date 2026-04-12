@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', 'Forbidden')
{{-- @section('message', __($exception->getMessage() ?: 'Forbidden')) --}}
