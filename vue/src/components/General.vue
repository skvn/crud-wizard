<template>
    <div role="tabpanel" class="tab-pane active" id="general">

        <form id="vf">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="name">Model name *</label>
                    {{$parent.model.acl }}
                    <input type="text" class="form-control"  v-model="$parent.model.name" readonly />
                </div>


                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="is_tree" value="1" name="is_tree"
                               v-model="$parent.model.tree"/>
                        <b>Model is tree <span class="text-danger">If the table doesn't contain `tree_path`, `tree_order`,
                                    `tree_pid`, `tree_depth` columns - a migration will be created</span></b>
                    </label>
                </div>


                <div class="form-group">
                    <label for="acl">Acl</label>
                    <select class="form-control default_select" id="acl" name="acl"
                            v-model="$parent.model.acl" style="width:250px;">
                        <option value="">Choose acl</option>
                        <option v-for="(acl, acl_title) in $parent.config.acls" v-bind:value="acl" :value="acl">
                            {{ acl_title }}
                        </option>

                    </select>
                </div>

                <div class="form-group">
                    <label for="ent_name">Entity name *</label>
                    <input type="text" class="form-control" id="ent_name" name="ent_name" required
                           v-model="$parent.model.ent_name"  />
                </div>
                <div class="form-group">
                    <label for="title_field">Title field</label>
                    <select class="form-control default_select"   v-model="$parent.model.title_field" style="width:250px;">
                        <option value="">Choose database table field</option>
                        <option v-for="(key, col) in $parent.config.table_columns" v-bind:value="col">
                            {{ col }}
                        </option>

                    </select>

                    <div class="help-block">DB table field that serves as title or name of the
                        entity
                    </div>
                </div>


                <div class="form-group">
                    <label for="ent_name_r">(Russian only) Model entity name (Родительный
                        падеж)</label>
                    <input type="text" class="form-control" id="ent_name_r" name="ent_name_r"
                           v-model="$parent.model.ent_name_r"/>
                </div>

                <div class="form-group">
                    <label for="ent_name_v">(Russian only) Model entity name (Винительный
                        падеж)</label>
                    <input type="text" class="form-control" id="ent_name_v" name="ent_name_v"
                           v-model="$parent.model.ent_name_v"/>
                </div>
                <div class="form-group">
                    <label for="dialog_width">Modal dialog width (optional)</label>
                    <input type="text" class="form-control" id="dialog_width"
                           name="dialog_width" v-model="$parent.model.dialog_width"/>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="dialog_width">Audit trail. Track model changes</label>
                    <select name="track_history" id="track_history"
                            class="form-control default_select" style="width:250px;"
                            v-model="$parent.model.track_history">
                        <option v-for="(value, text) in $parent.config.track_history_options" v-bind:value="value">
                            {{ text }}
                        </option>

                    </select>
                </div>
            </div>
        </div>
        </form>
    </div>
</template>
<script>


    export default {

        name: 'General',
        //store,


        created () {
            this.$nextTick(function () {
                $('form').first().bootstrapValidator();
            });
        }
    }
</script>
