<?php

namespace App\Http\Controllers;

use App\Models\TearmsCondition;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TermsAndConditionsController extends Controller {

	public function index() {

		$termconditions = TearmsCondition::first();

		return view("terms_condition.index") -> with("termconditions", $termconditions);
	}

	public function update(Request $request, $id) {
		
		$terms = $request -> terms_condition;
		$termcondition = TearmsCondition::find($id);

		if($termcondition) {
			$termcondition -> terms = $terms;
		}
		$termcondition -> save();

		// returned nothing before, so the save ended on a blank response with no
		// confirmation for the user
		// Saved over ajax; the page reports it. Returning nothing left the caller
		// with an empty body and no way to tell success from failure.
		return response() -> json(['message' => trans('lang.settings_have_been_saved_successfully')]);
	}
	
	public function indexPrivacy() {

		$privacyPolicy = PrivacyPolicy::first();
		
		return view("privacy_policy.index") -> with("privacyPolicy", $privacyPolicy);
	}
	
	public function updatePrivacy(Request $request, $id) {
		$privacy = $request -> privacy_policy;
		
		$privacyPolicy = PrivacyPolicy::find($id);
		if($privacyPolicy) {
			$privacyPolicy -> privacy_policy = $privacy;
		}
		$privacyPolicy -> save();

		// Saved over ajax; the page reports it. Returning nothing left the caller
		// with an empty body and no way to tell success from failure.
		return response() -> json(['message' => trans('lang.settings_have_been_saved_successfully')]);
	}

}
