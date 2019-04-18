<?php

namespace Encore\Action\Http\Controllers;

use App\ActionCode;
use App\ActionCodeCampaign;
use App\ActionCodeCase;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Repositories\Utils;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\CharsetConverter;
use Carbon\Carbon;

class ActionController extends Controller
{
    public function index(Content $content)
    {	
        $campaigns = ActionCodeCampaign::all();
        $arr = [];

        return $content
            ->header('Title')
            ->description('Description')
            ->body(view('action::index', compact('campaigns','arr')));
    }

    public function upload( Request $request, Content $content )
    {
    	if ( $request->action_file ) {

    		$validatedData = $request->validate( [
                'action_file' => 'required|file',
            ] );

            if ( $request->action_file->getClientOriginalExtension() !== "csv" ) {

            	return back()->withErrors(['action_file' => "The file must be the extension of csv."]);
            }

            $filename = md5( time() ) . '.' . $request->action_file->getClientOriginalExtension();

            $success = ActionCodeCampaign::check_duplicate_campaign( $request->action_file->getClientOriginalName() );

            if ( $success === true ) {

            	return back()->withErrors(['action_file' => "Duplicate file has been uploaded."]);
            }

            $request->action_file->move( public_path('uploads/actions'), $filename );

            $path = public_path('uploads/actions') ."/". $filename;
            $csv = Reader::createFromPath( $path, 'r' );
            
            $input_bom = $csv->getInputBOM();

            if ($input_bom === Reader::BOM_UTF16_LE || $input_bom === Reader::BOM_UTF16_BE) {

                CharsetConverter::addTo($csv, 'utf-16', 'utf-8');
            }

            $headers = array(
                "number", "trademark", "tm_filing_date", "tm_holder", "mark_current_status_code", "mark_current_status_date", "registration_date", "applicant_address", "applicant_city", "city_of_registration", "state","class_number", "class_description", "applicant_zip", "country", "case_number", "goods", "applicant_country_code"
            );

            $count    = 0;
            $success  = 0; //10 is success, if not fail
            $arr      = array();

            $last_action_code = ActionCode::max('id');

            foreach ( $csv as $data ) {

            	// dd($data);
                
                if ( $count == 0 ) {

                    foreach ( $data as $key ) {
                        
                        $result = array_search( $key, $headers );

                        if ( $result !== false ) {
                            $success++;
                        }
                    }

                    if ( $success < 9 ) {

                        break;
                    } 

                    $cm = ActionCodeCampaign::create( [
		                'name'      => $request->action_file->getClientOriginalName(),
		                'file_name' => $filename,
		                'count'     => $count 
		            ] );

                } else {
                    // dd($data);
                	$last_action_code++;

            		$data[0] = Utils::case_gen( $last_action_code );

                	$code = ActionCode::create( [

                		'case_number'             => Utils::case_gen( $last_action_code ),
                		'action_code_type_id'     => $request->type != 7 ? 8 : 7,
                		'action_code_campaign_id' => $cm->id,
                	] );

                	$case = ActionCodeCase::create( [
                		'action_code_id'          => $code->id,
                		'action_code_campaign_id' => $cm->id,
                		'number'                  => str_replace("'", "", $data[36]),
                        'trademark'               => $data[31],
                        'tm_filing_date'          => Carbon::createFromFormat('m/d/y', $data[28]),
                        'tm_holder'               => $data[30],
                        'application_reference'   => $data[7],
                        'mark_current_status_code'=> $data[12],
                        'mark_current_status_date'=> Carbon::createFromFormat('m/d/y', $data[13]),
                        'ref_processed_date'      => Carbon::createFromFormat('m/d/y H:i', $data[15]),
                        'registration_date'       => Carbon::createFromFormat('m/d/y', $data[28]),
                        'address'                 => $data[1],
                        'city'                    => $data[4],
                        'city_of_registration'    => $data[8],
                        'state'                   => $data[5], 
                        'class_number'            => $data[34],
                        'class_description'       => $data[33],
                        'representatives'         => null,
                        'email'                   => null,
                        'fax_no'                  => null,
                        'telephone_no'            => null,
                        'website'                 => null,
                        'zip'                     => $data[6],
                        'country'                 => $data[2],
                        'country_code'            => $data[3],
                	] );

                	array_push( $arr , $data);
                }
                
                $count++;
            }

            $campaigns = ActionCodeCampaign::all();

            return $content
                ->header('Action Code')
                ->description('View')
                ->body( view('action::index', compact( 'arr', 'campaigns' )) );
    	}
    }
}