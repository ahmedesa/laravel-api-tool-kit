<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Tests\Mocks\Models\TestModel;
use Illuminate\Database\Eloquent\Model;

class HasGeneratedCodeTraitTest extends TestCase
{
    /**
     * @test
     */
    public function refreshCodeGeneratesNewCode(): void
    {
        $model = TestModel::factory()->create();

        $oldCode = $model->code;
        $model->refreshCode();
        $newCode = $model->code;

        $this->assertNotEquals($oldCode, $newCode);
    }

    /**
     * @test
     */
    public function testGenerateCodeCreatesUniqueCodes(): void
    {
        $model1 = TestModel::factory()->create();
        $model2 = TestModel::factory()->create();

        $code1 = $model1->generateCode();
        $code2 = $model2->generateCode();

        $this->assertNotEquals($code1, $code2);
    }

    /**
     * @test
     */
    public function findByCodeScope(): void
    {
        $model = TestModel::factory()->create();

        $foundModel = $model->findByCode($model->code);

        $this->assertInstanceOf(Model::class, $foundModel);
        $this->assertEquals($model->getKey(), $foundModel->getKey());
    }
}
