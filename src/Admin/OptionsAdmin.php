<?php

namespace XT\Option\Admin;


use XT\Admin\Controller\AbstractPlugin;
use XT\Core\Common\Common;
use XT\Core\Controller\Plugin\askBeforeDone;
use XT\Core\System\RBAC_PERMISSION;
use XT\Core\ToolBox\MessageBox;
use XT\Option\Form\GroupoptionForm;
use XT\Option\Form\OptionForm;
use XT\Option\Model\Group;
use XT\Option\Model\Option;
use XT\Option\Service\Groups;
use XT\Option\Service\OptionManager;

class OptionsAdmin extends AbstractPlugin
{
    protected $nameplugin = 'Options';
    protected $description = 'Options for website (title, description, analytic code ..., product, article, contact ...)';
    protected $directory = "config";

    /**
     * @var Groups
     */
    protected $grouptable = null;

    /**
     * @var \XT\Option\Service\Options
     */
    protected $optionstable = null;

    /**
     * @var OptionManager
     */
    protected $optionmanager = null;


    public function _int()
    {
        $this->grouptable = $this->serviceManager->get(Groups::class);
        $this->optionstable = $this->serviceManager->get(\XT\Option\Service\Options::class);
        $this->optionmanager = $this->serviceManager->get(OptionManager::class);


    }

    public function intPlugin()
    {
        $ar = [];

        $ar['addgroup'] = [
            'name' => 'AddGroup',
            'description' => 'Add new group',
            'index' => true
        ];

        $ar['conf'] = [
            'name' => 'ListOptions',
            'description' => 'Optins in group',
            'index' => true
        ];
        $ar['addoption'] = [
            'name' => '+ add',
            'description' => 'Add new option in group',
            'index' => true
        ];
        $ar['editgroup'] = [
            'name' => '+ edit group',
            'description' => 'Edit  group',
            'index' => true
        ];
        $ar['editoption'] = [
            'name' => '+ edit option',
            'description' => 'Edit option',
            'index' => true
        ];



        $this->setListaction($ar);
        parent::intPlugin(); // TODO: Change the autogenerated stub
    }


    function index($id)
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;

        $this->_int();


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);


        $path = realpath(OptionManager::$fileconfig . '.back');
        $fileinfo = [];

        if ($path !== false) {
            $files = scandir($path);
            foreach ($files as $file) {
                $fullpath = $path . '/' . $file;
                if (is_file($fullpath)) {
                    $names = explode('.', $file);

                    $info = [
                        'file' => $file,
                        'date' => date('Y-m-d h:s', $names[0])
                    ];
                    $fileinfo[] = $info;
                }

            }
        }


        $listoption = $this->optionmanager->listoptions();


        $ar = [
            'listoption' => $listoption,
            'groups' => $this->grouptable->select(),
            'listfile' => $fileinfo
        ];


        $view->setVariables($ar);
        return $view;
    }

    function addgroup($id)
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();

        $request = Common::getRequest();
        $form = new GroupoptionForm('formgroup', $this->dbAdapter);
        $form->get('submit')->setValue('Add');

        if ($request->isPost()) {

            $group = new Group();
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $group->exchangeArray($form->getData());
                $this->grouptable->insert($group->getArrayCopy());
                return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options']);
            }
        }

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['form' => $form]);
        return $view;
    }

    function editgroup($id)
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();


        $request = Common::getRequest();

        $form = new GroupoptionForm('edit', $this->dbAdapter);
        $form->get('submit')->setValue('Update');
        $name = $id;

        $tableoption = $this->grouptable;
        $gr = $tableoption->findByName($name);

        if ($gr == null)
            return MessageBox::redirectMgs(
                "Group not exist!",
                $this->url('options')
            );


        if ($request->isPost()) {

            $group = new Group();
            $button = $request->getPost('submit', 'Update');

            if ($button == 'Update') {

                $form->setData($request->getPost());
                if ($form->isValid()) {

                    $group->exchangeArray($form->getData());

                    $data = $group->getArrayCopy();
                    unset($data['id']);

                    $this->grouptable->update($data, ['name' => $group->getName()]);
                    return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options']);
                }
            } else if ($button == "Delete") return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options', 'act' => 'delgroup', 'id' => $id]);

        } else $form->bind($gr);
        $form->get('name')->setAttribute('readonly', 'true');


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['form' => $form]);
        return $view;
    }

    function delgroup($id)
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();


        $request = Common::getRequest();
        $tableoption = $this->grouptable;
        /***
         * @var $gr Group
         */
        $gr = $tableoption->select(['name' => $id])->current();
        if ($gr == null)
            return MessageBox::redirectMgs(
                "Group not exist!",
                $this->url('options')
            );


        if ($request->isPost()) {

            if ($this->ctrl->isConfirm(['name' => $id, 'delete' => 'okdelete'])) {

                $rowaffect = $tableoption->delete(['name' => $id]);
                if ($rowaffect == 0)
                    return MessageBox::redirectMgs(
                        "Group cannot delete (delete option items before)!",
                        $this->url('options')
                    );

                $this->optionmanager->buildconfig();

                return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options']);

            }
            return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options']);

        }

        return $this->ctrl->askBeforeDone(
            'Delete group:' . $gr->getDescription(),
            $this->url('options', 'delgroup', $id),
            ['name' => $id, 'delete' => 'okdelete']

        );


    }

    function removeallbackup()
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();


        $request = Common::getRequest();

        if ($request->isPost()) {

            if ($this->ctrl->isConfirm(['delete' => 'okdelete'])) {

                $path = realpath(OptionManager::$fileconfig . '.back');
                if ($path !== false) {
                    $files = scandir($path);
                    $fileinfo = [];
                    foreach ($files as $file) {
                        $fullpath = $path . '/' . $file;
                        if (is_file($fullpath)) {
                            @unlink($fullpath);
                        }

                    }
                }


                return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options']);

            }
            return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options']);

        }

        return $this->ctrl->askBeforeDone(
            'Remove all backup option file (bak)',
            $this->url('options', 'removeallbackup'),
            ['delete' => 'okdelete']

        );


    }

    function restorebackup()
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();

        $file = $this->ctrl->params()->fromQuery('f', null);


        $request = Common::getRequest();
        if ($request->isPost()) {

            if ($this->ctrl->isConfirm(['filerestore' => $file])) {

                $fullfile = realpath(OptionManager::$fileconfig . '.back/' . $file);
                $content = file_get_contents($fullfile);
                $arr = include $fullfile;

                $groups = $arr['CF'];
                foreach ($groups as $group => $options) {

                    //Restore group if not exist
                    if (!$this->dbAdapter->existrow(['name' => $group], 'option_groups')) {
                        $this->dbAdapter->insert(
                            [
                                'name' => $group,
                                'description' => $group . ' backup'
                            ],
                            'option_groups'
                        );
                    }

                    $g = $this->grouptable->findByName($group);

                    //Restore
                    foreach ($options as $idoption => $option) {

                        if ($idoption == 'pathtemplatedefault' || $idoption == 'rootpath') continue;

                        if (!$this->dbAdapter->existrow(['name' => $idoption], 'option_items')) {
                            $this->dbAdapter->insert(
                                [
                                    'name' => $idoption,
                                    'description' => $idoption . ' backup',
                                    'hint' => $idoption,
                                    'value' => $option,
                                    'type' => 'string',
                                    'group_id' => $g->getId()
                                ],
                                'option_items'
                            );
                        } else
                            $this->dbAdapter->update(
                                [
                                    'value' => $option
                                ],
                                ['name' => $idoption], 'option_items');

                    }
                }

            }

            return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options']);

        } else
            return $this->ctrl->askBeforeDone(
                'Retore option ?',
                $this->ctrl->url()->fromRoute('admin', ['plugin' => 'options', 'act' => 'restorebackup'], ['query' => ['f' => $file]]),
                ['filerestore' => $file]
            );


    }

    function conf($id)
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();

        $params = $this->ctrl->params();
        $idgroup = $id;
        /***
         * @var $group Group
         */
        $group = $this->grouptable->findByName($idgroup);

        if ($group == null) {
            $url = $this->plugin('url')->fromRoute('ahdconfig', [], ['force_canonical' => true]);
            return $this->returnRedirect(['Thiết lập này không có'], 'Lỗi', $url, 20);
        }


        if ($this->ctrl->getRequest()->isPost()) {

            $update = $this->ctrl->getRequest()->getPost();
            foreach ($update as $k => $v) {
                $this->optionstable->saveOptionValue($k, $v);
            }
            $this->optionmanager->buildconfig();
            return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options']);
        }

        $options = $this->optionstable->select(['group_id' => $group->getId()]);

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['group' => $group, 'options' => $options]);
        return $view;


    }

    function addoption($id)
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();

        $params = $this->ctrl->params();
        $idgroup = $id;
        $request = $this->ctrl->getRequest();


        $group = $this->grouptable->findByName($idgroup);

        if ($group == null) {
            $url = $this->ctrl->plugin('url')->fromRoute('admin', ['plugin' => 'options'], ['force_canonical' => true]);
            return MessageBox::redirectMgs('Not found group', $url);
        }


        $form = new OptionForm();

        $form->get('group_id')->setValue($group->getId());
        $form->get('submit')->setValue('Add');


        if ($request->isPost()) {

            $option = new Option();
            $form->addNoRecordExistsName();
            //option_id
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $option->exchangeArray($form->getData());
                if ($option->getType() == 'boolean') $option->setValue(0); else $option->setValue('');
                $is = $option->getArrayCopy();

                $this->optionstable->insert($is);
                $this->optionmanager->buildconfig();
                return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options', 'act' => 'conf', 'id' => $group->getName()]);
            }
        }


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['form' => $form, 'group' => $group]);
        return $view;


    }

    function editoption($id)
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();

        $params = $this->ctrl->params();
        $name = $id;
        $request = $this->ctrl->getRequest();


        /***
         * @var $option Option
         */
        $option = $this->optionstable->findByName($name);

        if ($option == null) {
            $url = $this->ctrl->plugin('url')->fromRoute('admin', ['plugin' => 'options'], ['force_canonical' => true]);
            return MessageBox::redirectMgs('Not found option', $url);
        }

        $group = $this->grouptable->find($option->getGroupId());


        $form = new OptionForm();

        $form->get('group_id')->setValue($option->getGroupId());
        $form->get('submit')->setValue('Update');
        $idoriginOption = $option->getId();


        if ($request->isPost()) {


            $form->addNoRecordExistsName(['value' => $option->getId(), 'field' => 'id']);

            //option_id
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $option->exchangeArray($form->getData());
                if ($option->getType() == 'boolean') $option->setValue(0); else $option->setValue('');
                $is = $option->getArrayCopy();
                $is['id'] = $idoriginOption;


                $this->optionstable->update($is, ['id' => $idoriginOption]);
                $this->optionmanager->buildconfig();
                return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options', 'act' => 'conf', 'id' => $group->getName()]);
            }
        }

        $form->bind($option);


        $view = $this->createView(__DIR__, __CLASS__, 'addoption', __FUNCTION__);
        $view->setVariables(['form' => $form, 'group' => $group, 'option' => $option]);
        return $view;


    }

    function deleteoption($id)
    {
        if ($notAllow = $this->notAllow(RBAC_PERMISSION::OPTIONS_INDEX))
            return $notAllow;
        $this->_int();

        $params = $this->ctrl->params();
        $name = $id;
        $request = $this->ctrl->getRequest();


        /***
         * @var $option Option
         */
        $option = $this->optionstable->findByName($name);

        if ($option == null) {
            $url = $this->ctrl->plugin('url')->fromRoute('admin', ['plugin' => 'options'], ['force_canonical' => true]);
            return MessageBox::redirectMgs('Not found option', $url);
        }
        $group = $this->grouptable->find($option->getGroupId());


        if ($request->isPost()) {
            if ($this->ctrl->isConfirm(['name' => $option->getName()])) {
                $this->optionstable->delete(['id' => $option->getId()]);
                $this->optionmanager->buildconfig();
                return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'options', 'act' => 'conf', 'id' => $group->getName()]);

            }
        }

        return $this->ctrl->askBeforeDone(
            'Delete item:' . $option->getName(),
            $this->url('options', 'deleteoption', $option->getName()),
            ['name' => $option->getName()]
        );


    }

}