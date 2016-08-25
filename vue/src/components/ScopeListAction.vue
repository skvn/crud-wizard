<template>
  <tr>
    <td>
      <div class="form-group">
        <label>Action type
          <select class="form-control default_select" v-model="action_type">
            <option value="method">Model method</option>
            <option value="event">CRUD event</option>
            <option value="modal">Modal popup</option>

          </select>
        </label><br>

        <label v-if="action_type=='method'">
          Choose method *
          <!--<select class="form-control default_select" v-model="action.command" required>-->
          <!--<option value="">No method</option>-->
          <!--<option v-for="f in config.commands" v-bind:value="f.name">-->
          <!--{{ f.name }} ({{ f.description }})-->
          <!--</option>-->
          <!--</select>-->

          <input type="text" class="form-control" :value="action.command" v-model="action.command" name="command">

        </label>

        <label v-if="action_type=='event'">
          Event name * <input type="text" class="form-control" :value="action.event" v-model="action.event"
                              name="event">
        </label>

        <template v-if="action_type=='modal'">
          <label>
            Modal URL * <input type="text" class="form-control" :value="action.popup" v-model="action.popup"
                               name="popup">
          </label><br>
          <label>
            Modal ID:<input type="text" class="form-control" :value="action.popup_id" v-model="action.popup_id"
                            name="popup_id">
          </label>
        </template>
      </div>

    </td>
    <td>
      <label>Action title *</label>
      <input type="text" required class="form-control" :value="action.title" v-model="action.title" name="title">
      <label>
        Icon class <input type="text" class="form-control" :value="action.class" v-model="action.class" name="class">
      </label>
      <label>
        Button class <input type="text" class="form-control" :value="action.btn_class" v-model="action.btn_class"
                            name="btn_class">
      </label>
    </td>
    <td>
      <label>
        Appears if column
        <select class="form-control default_select" v-model="action.ifcolumn" name="ifcolumn">
          <option value="">Choose column</option>
          <option v-for="col in scope.list" v-bind:value="col">
            {{ col.data }}
          </option>
        </select>
        is not empty
      </label><br/>

      <label>
        Confirm <input type="text" class="form-control" :value="action.confirm" v-model="action.confirm" name="confirm">
      </label>
      <br/>
      <label class="checkbox-inline">
        <input type="checkbox" name="single" v-model="action.single"/>
        Single rows
      </label>
      <br/>
      <label class="checkbox-inline">
        <input type="checkbox" v-model="action.mass" name="mass"/>
        Mass actions
      </label>
    </td>
    <td>
      <a href="#" class="label label-danger" @click.prevent="$dispatch('list_action::delete',key)">
        <i class="fa fa-trash-o"></i></a>
    </td>
  </tr>
</template>

<script>

  import Vue from 'vue';
  import {getConfig, getModel} from '../vuex/getters'


  export default{

    name: 'ScopeListAction',

    vuex: {
      getters: {
        config: getConfig,
        model: getModel
      }

    },

    data(){
      return {
        action_type: 'method'
      }
    },


    ready() {

      if (typeof this.action.event != 'undefined' && this.action.event != '') {
        this.action_type = 'event';
      }

      if (typeof this.action.popup != 'undefined' && this.action.popup != '') {
        this.action_type = 'modal';
      }

      if (typeof this.action.ifcolumn == 'undefined') {
        this.action.ifcolumn = '';
      }

    },

    props: [
      'action',
      'scope',
      'key'
    ],


  }
</script>
