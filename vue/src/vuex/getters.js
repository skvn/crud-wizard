
export const getModel = state => state.model;
export const getConfig = state => state.config;
export const getRelations = state =>  {
    let relations = {};
    for (let k in state.model.fields) {
        if (state.model.fields[k].relation) {
            relations[k] = state.model.fields[k];
        }
    }

    return relations;
}