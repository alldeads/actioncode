<div class="box">
    <!-- /.box-header -->
    @if ( $errors->has('action_file') )
	    <div class="alert alert-danger">
	    	<strong>{{ $errors->first('action_file') }}</strong>
	    </div>
	@endif
    <div class="box-header with-border">

    	<div class="row">
    		<div class="col-md-2">
    			<div class="pull-left">
		    		<a href="#" class="btn btn-sm btn-info" title="Filter">
			    		<i class="fa fa-filter"></i>
			    		<span class="hidden-xs"> &nbsp;&nbsp; Filter</span>
			    	</a>
		    	</div>
    		</div>

    		<div class="col-md-8">
    			<div style="text-align: center;">
		    		<form id="campaignForm">
		    			<select class="form-control" id="campaign" name="campaign">
		    				<option> Select Campaign Name</option>
		    				@foreach ( $campaigns as $campaign )
		    					<option value="{{ $campaign->id }}"> {{ $campaign->name }}</option>
		    				@endforeach
		    			</select>
		    		</form>
		        </div>
    		</div>

    		<div class="col-md-2">
    			<div class="pull-right">
		        	<button  class="btn btn-sm btn-success" data-toggle="modal" data-target="#upload" title="Upload">
		        		<i class="fa fa-upload"></i>
		        		<span class="hidden-xs"> &nbsp;&nbsp; Upload</span>
		        	</button>
		        </div>
    		</div>
    	</div>
    </div>

    <!-- Modal -->
  	<div class="modal fade" id="upload" role="dialog">
	    <div class="modal-dialog">
	      	<div class="modal-content">
	        	<div class="modal-header">
	          		<button type="button" class="close" data-dismiss="modal">&times;</button>
	          		<h4 class="modal-title">Upload</h4>
	        	</div>
	        	<div class="modal-body">
	          		<form method="POST" action="/admin/action/upload" enctype="multipart/form-data">
	          			@csrf

	          			<input type="file" name="action_file" class="form-control" required>

	          			<br>

	          			<select name="type" class="form-control">
	          				<option value="7"> Default</option>
	          				<option value="8"> Priority Filing</option>
	          			</select>

	          			<button class="btn btn-info" type="submit" style="margin-top: 10px;"> Upload</button>
	          		</form>
	        	</div>
	        	<div class="modal-footer">
	          		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        	</div>
	     	 </div>
	    </div>
	 </div>

	 <table id="actioncode" class="display" style="width:100%">
	 	<thead>
	        <tr>
	            <th>Case Number</th>
	            <th>Trademark</th>
	        </tr>
    	</thead>
    	<tbody>
    		@foreach( $arr as $data )
    			<tr>
		            <td>{{ $data[0] }}</td>
		            <td>{{ $data[31] }}</td>
		        </tr>
    		@endforeach
	        
	    </tbody>
	 </table>
</div>