<div class="row">
    <div class="col-sm-9">
        <div class="panel panel-primary">
            <div class="panel-heading" onclick="hideFilter(this)">
                Selection Filter 
                <span class="glyphicon arrow glyphicon-chevron-up pull-right"></span>
            </div>
            <div class="panel-body">
                <form method="post" action="/acknowledgements/">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="col-sm-12 col-md-4">
                                {$form.full_name.label}
                            </label>
                            <div class="col-sm-12 col-md-8">
                                {$form.full_name.html}
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="col-sm-12 col-md-4">
                                {$form.citation_name.label}
                            </label>
                            <div class="col-sm-12 col-md-8">
                                {$form.citation_name.html}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="col-sm-12 col-md-4">
                                {$form.start_date.label}
                            </label>
                            <div class="col-sm-12 col-md-8">
                                {$form.start_date.html}
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="col-sm-12 col-md-4">
                                {$form.end_date.label}
                            </label>
                            <div class="col-sm-12 col-md-8">
                                {$form.end_date.html}
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="col-sm-12 col-md-4">
                                {$form.present.label}
                            </label>
                            <div class="col-sm-12 col-md-8">
                                {$form.present.html}
                            </div>
                        </div>
                    </div>
                    <br class="visible-xs">
                    <div id="advanced-buttons">
                        <div class="col-sm-4 col-md-3 col-xs-12 col-md-offset-6">
                            <input type="submit" name="filter" value="Show Data" id="showdata_advanced_options" class="btn btn-sm btn-primary col-xs-12" />
                        </div>

                        <div class="visible-xs col-xs-12"> </div>
                        <div class="visible-xs col-xs-12"> </div>
                        <div class="visible-xs col-xs-12"> </div>
                        <div class="visible-xs col-xs-12"> </div>
                        <div class="col-sm-4 col-md-3 col-xs-12">
                            <input type="button" name="reset" value="Clear Form" class="btn btn-sm btn-primary col-xs-12" onclick="location.href='{$baseurl}/acknowledgements/?reset=true'" />
                        </div>
                        <div class="visible-xs col-xs-12"> </div>
                        <div class="visible-xs col-xs-12"> </div>
                        <div class="visible-xs col-xs-12"> </div>
                        <div class="visible-xs col-xs-12"> </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="panel panel-primary">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="GET" action="/acknowledgements">
                            <div class="row">
                                <div class="col-lg-8 col-sm-8">
                                    <select id="select-center" name="center_id" class="form-control">
                                        <option id="select-center-prompt" value="0">Select a Center</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-sm-4">
                                    <input type="submit" value="Select Center" class="btn btn-sm btn-primary"/>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <span class="visible-sm">&nbsp;<br/></span>
                        <input type="button" id="btn-add-acknowledgement" value="Add Acknowledgement" class="btn btn-sm btn-primary" style="display: none;"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="tabs" style="background: white">
        <div class="tab-content">
            <div class="tab-pane active">
                <table class="table table-hover table-primary table-bordered table-acknowledgements dynamictable" border="0">
                    <thead>
                        <tr class="info">
                            <th>Citation Policy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td nowrap="nowrap">
                                <div class="col-sm-12 col-md-12">{$citation_policy}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--  title table with pagination -->
                <table id="LogEntries" border="0" valign="bottom" width="100%">
                    <tr>
                        <!-- display pagination links -->
                        <td align="right">{$page_links}</td>
                    </tr>
                </table>
                {foreach from=$form.errors item=error}
                <ul>
                    <li class="error">{$error}</li>
                </ul>
                {/foreach}
                <div id="acknowledgement-table-container">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="acknowledgement-form-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="acknowledgement-form-dialog-title"></h3>
            </div>
            <form id="acknowledgement-form">
                <input type="hidden" id="ack-id" name="id"/>
                <input type="hidden" id="ack-center-id" name="center_id"/>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="full_name">Full Name<font color="red"><sup> *</sup></font></label>
                            <div class="col-xs-8">
                                <input type="text" id="ack-full-name" name="full_name" placeholder="Full Name" class="form-fields form-control input-sm"/>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="citation_name">Citation Name<font color="red"><sup> *</sup></font></label>
                            <div class="col-xs-8">
                                <input type="text" id="ack-citation-name" name="citation_name" placeholder="Citation Name" class="form-fields form-control input-sm"/>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="affiliation_arr[]">Affiliations<font color="red"><sup> *</sup></font></label>
                            <div class="col-xs-8">
                                <select id="ack-affiliation-arr" name="affiliation_arr[]" class="form-fields form-control input-sm" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="degree_arr[]">Degrees<font color="red"><sup> *</sup></font></label>
                            <div class="col-xs-8">
                                <select id="ack-degree-arr" name="degree_arr[]" class="form-fields form-control input-sm" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="role_arr[]">Roles<font color="red"><sup> *</sup></font></label>
                            <div class="col-xs-8">
                                <select id="ack-role-arr" name="role_arr[]" class="form-fields form-control input-sm" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="start_date">Start Date<font color="red"><sup> *</sup></font></label>
                            <div class="col-xs-8">
                                <input type="date" id="ack-start-date" name="start_date" class="form-fields form-control input-sm" min="{$min_year}-01-01" max="{$max_year}-12-31"/>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="end_date">End Date<font color="red"><sup> *</sup></font></label>
                            <div class="col-xs-8">
                                <input type="date" id="ack-end-date" name="end_date" class="form-fields form-control input-sm" min="{$min_year}-01-01" max="{$max_year}-12-31"/>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="in_study_at_present">Present<font color="red"><sup> *</sup></font></label>
                            <div class="col-xs-8">
                                <select id="ack-in-study-at-present" name="in_study_at_present" class="form-fields form-control input-sm">
                                    <option value="">Unknown</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="acknowledgement-form-submit" role="button" aria-disabled="false"></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/acknowledgements/js/Row.js"></script>
<script src="/acknowledgements/js/TBody.js"></script>
<script src="/acknowledgements/js/Table.js"></script>
<script src="/acknowledgements/js/acknowledgements_helper.js"></script>
<script src="/acknowledgements/js/main.js"></script>