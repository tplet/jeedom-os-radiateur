<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('OSRadiator');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
		</div>
		<legend><i class="fas fa-clone"></i> {{Mes radiateurs à écran}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<br><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun radiateurs à écran trouvé, cliquer sur "Ajouter" pour commencer}}</div>';
		}
		else {
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			echo '<div class="eqLogicThumbnailContainer">';
            /*
             * List all eqLogic
             */
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				$file = 'plugins/OSRadiator/plugin_info/' . $eqLogic->getConfiguration('icon') . '.png';
				if (file_exists(__DIR__.'/../../../../'.$file)) {
					echo '<img src="'.$file.'" height="105" width="95">';
				}
				else {
					echo '<img src="' . $plugin->getPathImgIcon() . '">';
				}
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '<span class="hiddenAsCard displayTableRight hidden">';
				if ($eqLogic->getConfiguration('autorefresh', '') != '') {
					echo '<span class="label label-info">' .$eqLogic->getConfiguration('autorefresh') . '</span>';
				}
				echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Equipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Equipement non visible}}"></i>';
				echo '</span>';
				echo '</div>';
			}
			echo '</div>';
		}
		?>
	</div>

    <?php /* Create/Edit form eqLogic */ ?>
	<div class="col-xs-12 eqLogic" style="display:none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs">  {{Dupliquer}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#configurationTab" aria-controls="eqLogicConfiguration" role="tab" data-toggle="tab"><i class="fas fa-cog"></i> {{Configuration}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>
		<div class="tab-content" id="tabContent">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Nom du radiateur écran}}</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display:none;">
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du radiateur écran}}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" >{{Objet parent}}</label>
								<div class="col-sm-6">
									<select class="form-control eqLogicAttr" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php	$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Catégorie}}</label>
								<div class="col-sm-6">
									<?php	foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '">' . $value['name'];
										echo '</label>';
									}	?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Options}}</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>

							<legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Auto-actualisation}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Fréquence de rafraîchissement des commandes infos de l'équipement}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="autorefresh" placeholder="{{Cliquer sur ? pour afficher l'assistant cron}}">
										<span class="input-group-btn">
											<a id="bt_cronGenerator" class="btn btn-default cursor jeeHelper roundedRight" data-helper="cron" title="{{Assistant cron}}">
												<i class="fas fa-question-circle"></i>
											</a>
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<legend><i class="fas fa-info"></i> {{Informations}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Description de l'équipement}}</label>
								<div class="col-sm-6">
									<textarea class="form-control eqLogicAttr autogrow" data-l1key="comment"></textarea>
								</div>
							</div>
							<legend><i class="fas fa-at"></i> {{URL de retour}}</legend>
							<div class="form-group">
								<div class="alert alert-info col-xs-10 col-xs-offset-1 text-center callback">
									<span>
										<?php	echo network::getNetworkAccess('external') . '/core/api/jeeApi.php?plugin=OSRadiator&type=event&apikey=' . jeedom::getApiKey($plugin->getId()) . '&id=#cmd_id#&value=#value#';
										?>
									</span>
								</div>
							</div>
						</div>
					</fieldset>
				</form>
				<hr>
			</div>

            <div role="tabpanel" class="tab-pane" id="configurationTab">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="col-lg-6">
                            <legend><i class="fas fa-cogs"></i> {{Informations}}</legend>
                            <div class="form-group">
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Température}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenTemperature" placeholder="{{Température}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenTemperature"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Consigne}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenConsigne" placeholder="{{Consigne}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenConsigne"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Chauffage : Mode}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenChauffageMode" placeholder="{{Mode}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenChauffageMode"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Chauffage : Sous-mode (si existant)}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenChauffageSousMode" placeholder="{{Sous-mode ou vide}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenChauffageSousMode"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Chauffage : On/Off}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenChauffageOnOff" placeholder="{{On/Off}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenChauffageOnOff"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Radiateur : état brut}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenRadiatorState" placeholder="{{Etat}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenRadiatorState"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Sélection HAUT}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenButtonUP" placeholder="{{Sélection HAUT}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenButtonUP"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Sélection BAS}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenButtonDOWN" placeholder="{{Sélection BAS}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenButtonDOWN"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Sélection GAUCHE}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenButtonLEFT" placeholder="{{Sélection GAUCHE}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenButtonLEFT"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Sélection DROITE}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenButtonRIGHT" placeholder="{{Sélection DROITE}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenButtonRIGHT"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Sélection CLICK}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenButtonCLICK" placeholder="{{Sélection CLICK}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementInfo btn" data-input="screenButtonCLICK"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <legend><i class="fas fa-cogs"></i> {{Actions}}</legend>
                            <div class="form-group">
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Thermostat}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenThermostat" placeholder="{{Thermostat}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementAction btn" data-input="screenThermostat"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-4 control-label">{{Backlog0}}</label>
                                    <div class="col-sm-6 input-group">
                                        <input type="text" class="eqLogicAttr formAttr form-control" data-l1key="configuration" data-l2key="screenBacklog0" placeholder="{{Backlog0}}"/>
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listEquipementAction btn" data-input="screenBacklog0"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>

			<div role="tabpanel" class="tab-pane" id="commandtab">
				<div class="input-group pull-right" style="display:inline-flex;margin-top:5px;">
					<span class="input-group-btn">
						<a class="btn btn-info btn-xs roundedLeft" id="bt_addOSRadiatorInfo"><i class="fas fa-plus-circle"></i> {{Ajouter une info}}
						</a><a class="btn btn-warning btn-xs roundedRight" id="bt_addOSRadiatorAction"><i class="fas fa-plus-circle"></i> {{Ajouter une action}}</a>
					</span>
				</div>
				<br><br>
				<div class="table-responsive">
					<table id="table_cmd" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th class="hidden-xs" style="min-width:50px;width:70px;"> ID</th>
								<th style="min-width:150px;width:300px;">{{Nom}}</th>
								<th style="width:130px;">{{Type}}</th>
								<th style="min-width:180px;">{{Valeur}}</th>
								<th style="min-width:130px;width:250px;">{{Paramètres}}</th>
								<th style="min-width:260px;width:310px;">{{Options}}</th>
								<th style="min-width:80px;width:200px;">{{Actions}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>
<?php
include_file('desktop', 'OSRadiator', 'js', 'OSRadiator');
include_file('core', 'plugin.template', 'js');
?>
