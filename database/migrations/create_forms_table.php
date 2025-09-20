public function up()
{
    Schema::create('forms', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->timestamps();
    });
}
