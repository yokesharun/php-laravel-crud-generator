<!DOCTYPE html>
<html>
<head>
	<title>Laravel CRUD Generator</title>
	<link rel="stylesheet" type="text/css" href="https://bootswatch.com/paper/bootstrap.min.css">
</head>
<body>
	<div>
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="javascript:void(0);">Laravel CRUD Generator</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="https://github.com/yokesharun/php-laravel-crud-generator/fork" target="_blank">Fork it on Github</a></li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
	<div class="container">

		@if(Request::has('model') && Request::has('table'))

		<div style="margin-bottom: 20px;">
			<a href="{{route('laravelcrud.index')}}" class="btn btn-primary">< Back</a>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">{{Request::get('table')}} Table Columns </div>
			<div class="panel-body">
				@if(count($Columns) < 0)
					<p>No Columns Found</p>
				@else
					<form method="POST" action="{{route('laravelcrud.submit')}}">
						{{csrf_field()}}
						<input type="hidden" name="table" value="{{Request::get('table')}}">
						<input type="hidden" name="model" value="{{Request::get('model')}}">
						<table class="table table-striped table-hover ">
						  <thead>
						    <tr>
						      <th>#</th>
						      <th>Column Name</th>
						      <th>Column Type</th>
						      <th>Include / Exclude</th>
						      <th>Validation</th>
						    </tr>
						  </thead>
						  <tbody>
						  	@foreach($Columns as $index => $Column)
							    <tr>
							      <td>{{ $index + 1 }}</td>
							      <td>{{ $Column }}</td>
							      <td>
							      	<select class="form-control" name="{{$Column}}_type">
							      		<option value="TEXT">Text</option>
							      		<option value="TEXTAREA">Textarea</option>
							        </select>
							      </td>
							      <td>
							      	<div class="checkbox">
							          <label>
							            <input type="checkbox" 
							            	name="{{$Column}}_include" 
								            @if($Column != 'id' && $Column != 'created_at' && $Column != 'updated_at' && $Column != 'password' && $Column != 'remember_token')
								            	checked="true"
								            @endif
							            > Include
							          </label>
							        </div>
							      </td>
							      <td>
							      	<div class="checkbox">
							          <label>
							            <input type="checkbox" 
							            	name="{{$Column}}_required" 
								            @if($Column != 'id' && $Column != 'created_at' && $Column != 'updated_at' && $Column != 'password' && $Column != 'remember_token')
								            	checked="true"
								            @endif
							            > Required
							          </label>
							        </div>
							      </td>
							    </tr>
						    @endforeach
						  </tbody>
						</table> 
						<div class="form-group">
							<input type="submit" value="Generate Resource and View Files" class="btn btn-success">
						</div>
					<form>
				@endif

				</div>
			</div>
		@else
			<div class="panel panel-default">
				<div class="panel-heading">Generator Wizard</div>
				<div class="panel-body">
					<form action="{{route('laravelcrud.index')}}" method="GET" class="form-group">
						<div class="form-group">
							<label class="control-label" for="model">Enter Model Name</label>
							<input type="text" name="model" class="form-control" id="model">
						</div>
						<div class="form-group">
							<label class="control-label" for="table">Enter Table Name</label>
							<input type="text" name="table" class="form-control" id="table">
						</div>
						<div class="form-group">
							<input type="submit" value="Submit" class="btn btn-success">
						</div>
					</form>
				</div>
			</div>
		@endif
	</div>
</body>
</html>