export const getModel = state => state.model
export const getTable = state => state.table
export const getModels = state => state.models
export const getConfig = state => state.config
export const configLoaded = state => state.configLoaded
export const modelsLoaded = state => state.modelsLoaded
export const getRelations = state => {
  let relations = {}
  for (let k in state.model.fields) {
    if (state.model.fields[k].relation) {
      relations[k] = state.model.fields[k]
    }
  }

  return relations
}
